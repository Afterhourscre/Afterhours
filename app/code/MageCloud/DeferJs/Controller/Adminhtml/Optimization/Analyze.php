<?php
/**
 * @author andy
 * @email andyworkbase@gmail.com
 * @team MageCloud
 */
namespace MageCloud\DeferJs\Controller\Adminhtml\Optimization;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\LocalizedException;
use MageCloud\DeferJs\Helper\Data as DataHelper;
use Magento\Framework\App\Config\ConfigResource\ConfigInterface;
use MageCloud\DeferJs\Helper\Optimization as OptimizationHelper;
use MageCloud\DeferJs\Helper\ScreenShot;
use Magento\Framework\App\DeploymentConfig\Reader;
use Magento\Framework\App\State;
use Magento\Framework\Config\File\ConfigFilePool;
use Magento\Framework\Indexer\ConfigInterface as IndexerConfigInterface;

/**
 * Class Analize
 * @package MageCloud\DeferJs\Controller\Adminhtml\Optimization
 */
class Analyze extends \MageCloud\DeferJs\Controller\Adminhtml\SpeedTest
{
    const PARAM_TYPE_CACHE = 'cache';
    const PARAM_TYPE_FLAT = 'flat';
    const PARAM_TYPE_JS = 'js';
    const PARAM_TYPE_CSS = 'css';
    const PARAM_TYPE_INDEXING = 'indexing';
    const PARAM_TYPE_INDEXER = 'indexer';
    const PARAM_TYPE_PRODUCTION = 'production';

    /**
     * @var \Magento\Framework\App\Cache\TypeListInterface
     */
    protected $_cacheTypeList;

    /**
     * @var \Magento\Framework\App\Cache\StateInterface
     */
    protected $_cacheState;

    /**
     * @var \Magento\Framework\App\Cache\Frontend\Pool
     */
    protected $_cacheFrontendPool;

    /**
     * @var OptimizationHelper
     */
    protected $_optimizationHelper;

    /**
     * @var array
     */
    protected $_params = [];

    /**
     * @var array
     */
    protected $_availableTypes = [
        self::PARAM_TYPE_CACHE,
        self::PARAM_TYPE_FLAT,
        self::PARAM_TYPE_JS,
        self::PARAM_TYPE_CSS,
        self::PARAM_TYPE_INDEXING,
        self::PARAM_TYPE_INDEXER,
        self::PARAM_TYPE_PRODUCTION
    ];

    /**
     * @var \Magento\Framework\App\DeploymentConfig\Reader
     */
    protected $_reader;

    /**
     * @var \Magento\Framework\Indexer\ConfigInterface
     */
    private $config;

    /**
     * @var array
     */
    private $sharedIndexesComplete = [];

    /**
     * Analyze constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param DataHelper $dataHelper
     * @param ConfigInterface $configInterface
     * @param \Magento\Framework\HTTP\Adapter\Curl $curl
     * @param \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList
     * @param \Magento\Framework\App\Cache\StateInterface $cacheState
     * @param \Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool
     * @param OptimizationHelper $optimizationHelper
     * @param Reader $reader
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        DataHelper $dataHelper,
        ConfigInterface $configInterface,
        \Magento\Framework\HTTP\Adapter\Curl $curl,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Framework\App\Cache\StateInterface $cacheState,
        \Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool,
        OptimizationHelper $optimizationHelper,
        \Magento\Framework\App\DeploymentConfig\Reader $reader
    ) {
        parent::__construct(
            $context,
            $resultPageFactory,
            $dataHelper,
            $configInterface,
            $curl
        );
        $this->_cacheTypeList = $cacheTypeList;
        $this->_cacheState = $cacheState;
        $this->_cacheFrontendPool = $cacheFrontendPool;
        $this->_optimizationHelper = $optimizationHelper;
        $this->_reader = $reader;
    }

    /**
     * @param $key
     * @param null $value
     * @return $this
     */
    public function setParams($key, $value = null)
    {
        if ($key === (array)$key) {
            $this->_params = $key;
        } else {
            $this->_params[$key] = $value;
        }

        return $this;
    }

    /**
     * @param $key
     * @return mixed|null
     */
    public function getParamByKey($key)
    {
        if (isset($this->_params[$key])) {
            return $this->_params[$key];
        }

        return null;
    }

    /**
     * @return \Magento\Framework\DataObject
     */
    public function getParams()
    {
        $dataObject = new \Magento\Framework\DataObject();
        $dataObject->setData($this->_params);
        return $dataObject;
    }

    /**
     * @return bool
     */
    public function execute()
    {
        if (!$this->_formKeyValidator->validate($this->getRequest())) {
            return $this->getResponse()->setBody(_('Invalid form key. Please refresh the page.'));
        };

        // check caches
        $this->_cacheAnalyze();
        // check category/product flat enabled
        $this->_isFlatCatalogEnabled();
        // check js optimization enabled
        $this->_isJsOptimizationEnabled();
        // check css optimization enabled
        $this->_isCssOptimizationEnabled();
        // check cdn enabled
        $this->_isCDNEnabled();
        // check if asynchronous indexing for grid enabled
        $this->_isAsynchronousIndexingEnable();
        // check indexers mode
        $this->_checkIndexersMode();
        // check mode
        $this->_isProductionModeEnable();

        // build result html
        $output = $this->_buildResult();
        $this->getResponse()->setBody(
            (strlen($output) > 0) ? $output : __('Analyze return empty results. Please try again.')
        );
    }

    /**
     * @return $this
     */
    protected function _cacheAnalyze()
    {
        $cacheParams = [];
        $cacheTypes = $this->_cacheTypeList->getTypes();
        if (count($cacheTypes) > 0) {
            foreach ($cacheTypes as $code => $data) {
                if (!$this->_cacheState->isEnabled($code)) {
                    $cacheParams[] = [
                        'code' => $code,
                        'label' => __(strtolower($data->getCacheType()))
                    ];
                }
            }
            if (count($cacheParams) > 0) {
                $type = self::PARAM_TYPE_CACHE;
                $cacheParams = [
                    'title' => __('Enable cache for:'),
                    'can_apply' => in_array($type, $this->_availableTypes) ? true : false,
                    'items' => $cacheParams,
                    'url' => $this->_buildUrl(
                        [
                            'cache' => 'index'
                        ]
                    ),
                ];

                $this->setParams($type, $cacheParams);
            }
        }

        return $this;
    }

    /**
     * @return $this
     */
    protected function _isFlatCatalogEnabled()
    {
        $flatCategoryEnabled = $this->_optimizationHelper->getConfigFlag(
            OptimizationHelper::XML_PATH_FLAT_CATALOG_CATEGORY
        );
        $flatProductEnabled = $this->_optimizationHelper->getConfigFlag(
            OptimizationHelper::XML_PATH_FLAT_CATALOG_PRODUCT
        );

        $flatParams = [];
        if (!$flatCategoryEnabled) {
            $flatParams[] = [
                'code' => 'flat_category',
                'label' => __('catalog category')
            ];
        }
        if (!$flatProductEnabled) {
            $flatParams[] = [
                'code' => 'flat_product',
                'label' => __('catalog product')
            ];
        }

        if (!empty($flatParams)) {
            $type = self::PARAM_TYPE_FLAT;
            $flatParams = [
                'title' => __('Use flat for:'),
                'can_apply' => in_array($type, $this->_availableTypes) ? true : false,
                'items' => $flatParams
            ];

            $this->setParams('flat', $flatParams);
        }

        return $this;
    }

    /**
     * @return $this
     */
    protected function _isJsOptimizationEnabled()
    {
        $mergeJsEnabled = $this->_optimizationHelper->getConfigFlag(
            OptimizationHelper::XML_PATH_DEV_JS_MERGE_FILES
        );
        $bundlingJsEnabled = $this->_optimizationHelper->getConfigFlag(
            OptimizationHelper::XML_PATH_DEV_JS_BUNDLING_FILES
        );
        $minifyJsEnabled = $this->_optimizationHelper->getConfigFlag(
            OptimizationHelper::XML_PATH_DEV_JS_MINIFY_FILES
        );

        $jsParams = [];
        if (!$mergeJsEnabled) {
            $jsParams[] = [
                'code' => 'js_merge',
                'label' => __('erge javascript files')
            ];
        }
        if (!$bundlingJsEnabled) {
            $jsParams[] = [
                'code' => 'js_bundling',
                'label' => __('enable javascript bundling')
            ];
        }
        if (!$minifyJsEnabled) {
            $jsParams[] = [
                'code' => 'js_minify',
                'label' => __('minify javascript files')
            ];
        }

        if (!empty($jsParams)) {
            $type = self::PARAM_TYPE_JS;
            $jsParams = [
                'title' => __('Optimize you JS:'),
                'can_apply' => in_array($type, $this->_availableTypes) ? true : false,
                'items' => $jsParams
            ];

            $this->setParams('js', $jsParams);
        }

        return $this;
    }

    /**
     * @return $this
     */
    protected function _isCssOptimizationEnabled()
    {
        $mergeCssEnabled = $this->_optimizationHelper->getConfigFlag(
            OptimizationHelper::XML_PATH_DEV_CSS_MERGE_CSS_FILES
        );
        $minifyCssEnabled = $this->_optimizationHelper->getConfigFlag(
            OptimizationHelper::XML_PATH_DEV_CSS_MINIFY_FILES
        );

        $cssParams = [];
        if (!$mergeCssEnabled) {
            $cssParams[] = [
                'code' => 'css_merge',
                'label' => __('merge css files')
            ];
        }
        if (!$minifyCssEnabled) {
            $cssParams[] = [
                'code' => 'css_minify',
                'label' => __('minify css files')
            ];
        }

        if (!empty($cssParams)) {
            $type = self::PARAM_TYPE_CSS;
            $cssParams = [
                'title' => __('Optimize your CSS:'),
                'can_apply' => in_array($type, $this->_availableTypes) ? true : false,
                'items' => $cssParams
            ];

            $this->setParams('css', $cssParams);
        }

        return $this;
    }

    /**
     * @return $this
     */
    protected function _isCDNEnabled()
    {
        $baseUrl = $this->getBaseUrl();
        if (strpos($baseUrl, 'https') === false) {
            //ssl enabled
            $urlForStaticFiles = $this->_optimizationHelper->getConfigValue(
                OptimizationHelper::XML_PATH_WEB_UNSECURE_BASE_STATIC_URL
            );
            $urlForMediaFiles = $this->_optimizationHelper->getConfigValue(
                OptimizationHelper::XML_PATH_WEB_UNSECURE_BASE_MEDIA_URL
            );
        } else {
            $urlForStaticFiles = $this->_optimizationHelper->getConfigValue(
                OptimizationHelper::XML_PATH_WEB_SECURE_BASE_STATIC_URL
            );
            $urlForMediaFiles = $this->_optimizationHelper->getConfigValue(
                OptimizationHelper::XML_PATH_WEB_SECURE_BASE_MEDIA_URL
            );
        }

        $useCDNForStatic = false;
        $useCDNForMedia = false;
        // check if cdn use for static files
        if ((($urlForStaticFiles !== '') || ($urlForStaticFiles !== null))
            && (strpos($urlForStaticFiles, $baseUrl) !== false)
        ) {
            $useCDNForStatic = true;
        }

        // check if cdn use for media files
        if ((($urlForMediaFiles !== '') || ($urlForMediaFiles !== null))
            && (strpos($urlForMediaFiles, $baseUrl) !== false)
        ) {
            $useCDNForMedia = true;
        }

        $cdnParams = [];
        if (!$useCDNForStatic) {
            $cdnParams[] = [
                'code' => 'cdn_static',
                'label' => __('static view files')
            ];
        }

        if (!$useCDNForMedia) {
            $cdnParams[] = [
                'code' => 'cdn_media',
                'label' => __('media files')
            ];
        }

        if (!empty($cdnParams)) {
            $type = 'cdn';
            $cdnParams = [
                'title' => __('Use CDN for:'),
                'can_apply' => in_array($type, $this->_availableTypes) ? true : false,
                'items' => $cdnParams
            ];

            $this->setParams('cdn', $cdnParams);
        }

        return $this;
    }

    /**
     * @return $this
     */
    protected function _isAsynchronousIndexingEnable()
    {
        $asynchronousIndexingEnabled = $this->_optimizationHelper->getConfigFlag(
            OptimizationHelper::XML_PATH_DEV_GRID_ASYNC_INDEXING
        );

        if (!$asynchronousIndexingEnabled) {
            $type = self::PARAM_TYPE_INDEXING;
            $indexingParams = [
                'title' => __('Use Asynchronous Indexing'),
                'can_apply' => in_array($type, $this->_availableTypes) ? true : false
            ];

            $this->setParams('asynchronous_indexing', $indexingParams);
        }

        return $this;
    }

    /**
     * @return $this
     */
    protected function _checkIndexersMode()
    {
        $indexers = $this->_getAllIndexers();
        $indexersParams = [];
        foreach ($indexers as $indexer) {
            if (!$indexer->isScheduled()) {
                $indexersParams[] = [
                    'code' => $indexer->getId(),
                    'label' => strtolower($indexer->getTitle())
                ];
            }
        }

        if (!empty($indexersParams)) {
            $type = self::PARAM_TYPE_INDEXER;
            if (count($indexers) == count($indexersParams)) {
                // all indexers ara on 'update on save' mode
                $indexersParams = [
                    'title' => __('Use “Update on Schedule” mode for all indexers'),
                    'can_apply' => in_array($type, $this->_availableTypes) ? true : false
                ];
            } else {
                // specific indexer(s) ara on 'update on save' mode
                $indexersParams = [
                    'title' => __('Use “Update on Schedule” mode for indexer:'),
                    'can_apply' => in_array($type, $this->_availableTypes) ? true : false,
                    'items' => $indexersParams
                ];
            }

            $this->setParams('indexers', $indexersParams);
        }

        return $this;
    }

    /**
     * @return $this
     */
    protected function _reindexAllIndexers()
    {
        $indexers = $this->_getAllIndexers();
        foreach ($indexers as $indexer) {
            try {
                $this->validateIndexerStatus($indexer);
                $startTime = microtime(true);
                $indexerConfig = $this->getConfig()->getIndexer($indexer->getId());
                $sharedIndex = $indexerConfig['shared_index'];
                // skip indexers having shared index that was already complete
                if (!in_array($sharedIndex, $this->sharedIndexesComplete)) {
                    if (!$indexer->isScheduled()) {
                        // swicth only needed indexer
                        $indexer->setScheduled(true);
                    }
                    if ($sharedIndex) {
                        $this->validateSharedIndex($sharedIndex);
                    }
                }
                $resultTime = microtime(true) - $startTime;
                $time = gmdate('H:i:s', $resultTime);
            } catch (LocalizedException $e) {

            } catch (\Exception $e) {

            }
        }

        return $this;
    }

    /**
     * Get indexer ids that have common shared index
     *
     * @param string $sharedIndex
     * @return array
     */
    private function getIndexerIdsBySharedIndex($sharedIndex)
    {
        $indexers = $this->getConfig()->getIndexers();
        $result = [];
        foreach ($indexers as $indexerConfig) {
            if ($indexerConfig['shared_index'] == $sharedIndex) {
                $result[] = $indexerConfig['indexer_id'];
            }
        }
        return $result;
    }

    /**
     * Validate indexers by shared index ID
     *
     * @param string $sharedIndex
     * @return $this
     */
    private function validateSharedIndex($sharedIndex)
    {
        if (empty($sharedIndex)) {
            throw new \InvalidArgumentException('sharedIndex must be a valid shared index identifier');
        }
        $indexerIds = $this->getIndexerIdsBySharedIndex($sharedIndex);
        if (empty($indexerIds)) {
            return $this;
        }
        $indexerFactory = $this->_objectManager->create('Magento\Indexer\Model\IndexerFactory');
        foreach ($indexerIds as $indexerId) {
            /** @var \Magento\Indexer\Model\Indexer $indexer */
            $indexer = $indexerFactory->create();
            $indexer->load($indexerId);
            /** @var \Magento\Indexer\Model\Indexer\State $state */
            $state = $indexer->getState();
            $state->setStatus(\Magento\Framework\Indexer\StateInterface::STATUS_VALID);
            $state->save();
        }
        $this->sharedIndexesComplete[] = $sharedIndex;

        return $this;
    }

    /**
     * Get config
     *
     * @return \Magento\Framework\Indexer\ConfigInterface
     * @deprecated
     */
    private function getConfig()
    {
        if (!$this->config) {
            $this->config = $this->_objectManager->get(IndexerConfigInterface::class);
        }
        return $this->config;
    }

    /**
     * Returns all indexers
     *
     * @return \Magento\Framework\Indexer\IndexerInterface[]
     */
    protected function _getAllIndexers()
    {
        $collectionFactory = $this->_objectManager->create('Magento\Indexer\Model\Indexer\CollectionFactory');
        return $collectionFactory->create()->getItems();
    }

    /**
     * Validate that indexer is not locked
     *
     * @param \Magento\Framework\Indexer\IndexerInterface $indexer
     * @return void
     * @throws LocalizedException
     */
    private function validateIndexerStatus(\Magento\Framework\Indexer\IndexerInterface $indexer)
    {
        if ($indexer->getStatus() == \Magento\Framework\Indexer\StateInterface::STATUS_WORKING) {
            throw new LocalizedException(
                __(
                    '%1 index is locked by another reindex process. Skipping.',
                    $indexer->getTitle()
                )
            );
        }
    }

    /**
     * @return $this
     */
    protected function _isProductionModeEnable()
    {
        $env = false;;
        try {
            $env = $this->_reader->load(ConfigFilePool::APP_ENV);
        } catch (\Exception $e) {
            $modeParams = [
                'title' => __('Can\'t retrieve information about mode state'),
            ];
            $this->setParams('mode', $modeParams);
            return $this;
        }

        $mode = isset($env[State::PARAM_MODE]) ? $env[State::PARAM_MODE] : null;
        $currentMode = $mode ?: State::MODE_DEFAULT;

        if (($currentMode == State::MODE_DEVELOPER) || ($currentMode == State::MODE_DEFAULT)) {
            $type = self::PARAM_TYPE_PRODUCTION;
            $modeParams = [
                'title' => __('Switch to Production Mode'),
                'can_apply' => in_array($type, $this->_availableTypes) ? true : false
            ];
            $this->setParams('mode', $modeParams);
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getWebsiteScreenShot()
    {
        $screenShot = new ScreenShot($this->getBaseUrl());
        $screenShot->setParam('width', '500');
        $screenShot->capturePage();

        return $screenShot->getScreenShotUrl();
    }

    /**
     * @TODO - move to template file
     * @return string
     */
    protected function _buildResult()
    {
        $output = '';
        $params = $this->getParams()->getData();
        if ($params) {
            // website screenshot
//            $output .= '<div class="screenshot">';
//            $output .= '<img src="' . $this->getWebsiteScreenShot() . '"/>';
//            $output .= '</div>';
            $output .= '<section class="solutions">';
            $output .= '<h2>' . __('Recommended solution(s) for optimization:') . '</h2>';
            $output .= '<ul class="items">';
            foreach ($params as $type => $data) {
                $markedClassName = $this->checkAvailability($data);
                if (!isset($data['items']) && empty($data['items'])) {
                    $output .= '<li class="item ' . $markedClassName . '">' .
                        '<span class="title">' . $data['title'] . '</span>' . '</li>';
                } else {
                    $output .= '<li class="item ' . $markedClassName . '">';
                    $output .= '<span class="title">' . $data['title'] . '</span>';
                    $output .= '<ul class="sub-list">';
                    foreach ($data['items'] as $item) {
                        $output .= '<li class="sub-item">' . '<span>' . $item['label'] . '</span>' . '</li>';
                    }
                    $output .= '</ul>';
                    $output .= '</li>';
                }
            }
            $output .= '</ul>';
            $output .= '<p class="apply-tip marked">';
            $output .= __('The solution(s) may be applied by action below');
            $output .= '</p>';
            $output .= '</section>';
        }

        return $output;
    }

    /**
     * @param $routeParams
     * @return $this|string
     */
    protected function _buildUrl($routeParams)
    {
        if (!is_array($routeParams) || empty($routeParams)) {
            return $this->getBaseUrl();
        }

        $env = false;;
        try {
            $env = $this->_reader->load(ConfigFilePool::APP_ENV);
        } catch (\Exception $e) {

        }

        $backendPath = isset($env['backend']['frontName']) ? $env['backend']['frontName'] : null;
        if (null !== $backendPath) {
            $routeParams[$backendPath] = 'admin';
            $routeParams = array_reverse($routeParams);
//            array_unshift($routeParams, $backendPath . '/' . 'admin');
//            $routeParams = array_merge_recursive($routeParams, $routeParams);
        }

        $routePath = str_replace('=', '/',
            urldecode(http_build_query($routeParams, null, '/')));

//        return $this->getBaseUrl() . $routePath;
        return $routeParams;
    }

    /**
     * @param $data
     * @return string
     */
    public function checkAvailability($data)
    {
        return (isset($data['can_apply']) && !empty($data['can_apply'])) ? 'marked' : '';
    }

    /**
     * Check whether specified cache types exist
     *
     * @param array $types
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _validateTypes(array $types)
    {
        if (empty($types)) {
            return;
        }
        $allTypes = array_keys($this->_cacheTypeList->getTypes());
        $invalidTypes = array_diff($types, $allTypes);
        if (count($invalidTypes) > 0) {
            throw new LocalizedException(__('Specified cache type(s) don\'t exist: %1', join(', ', $invalidTypes)));
        }
    }
}