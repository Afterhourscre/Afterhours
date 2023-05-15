<?php
/**
 * @author andy
 * @email andyworkbase@gmail.com
 * @team MageCloud
 */
namespace MageCloud\DeferJs\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Config\ConfigResource\ConfigInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Class Optimization
 * @package MageCloud\DeferJs\Helper
 */
class Optimization extends AbstractHelper
{
    /**
     * Parameters XML paths
     */
    CONST XML_PATH_FLAT_CATALOG_CATEGORY = 'catalog/frontend/flat_catalog_category';
    CONST XML_PATH_FLAT_CATALOG_PRODUCT = 'catalog/frontend/flat_catalog_product';

    CONST XML_PATH_DEV_JS_MERGE_FILES = 'dev/js/merge_files';
    CONST XML_PATH_DEV_JS_BUNDLING_FILES = 'dev/js/enable_js_bundling';
    CONST XML_PATH_DEV_JS_MINIFY_FILES = 'dev/js/minify_files';
    CONST XML_PATH_DEV_CSS_MERGE_CSS_FILES = 'dev/css/merge_css_files';
    CONST XML_PATH_DEV_CSS_MINIFY_FILES = 'dev/css/minify_files';
    const XML_PATH_DEV_GRID_ASYNC_INDEXING = 'dev/grid_async/indexing';

    const XML_PATH_WEB_UNSECURE_BASE_STATIC_URL = 'web/unsecure/base_static_url';
    const XML_PATH_WEB_UNSECURE_BASE_MEDIA_URL = 'web/unsecure/base_media_url';
    const XML_PATH_WEB_SECURE_BASE_STATIC_URL = 'web/secure/base_static_url';
    const XML_PATH_WEB_SECURE_BASE_MEDIA_URL = 'web/secure/base_media_url';

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var ConfigInterface
     */
    protected $_configInterface;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        ConfigInterface $configInterface
    ) {
        parent::__construct($context);
        $this->_scopeConfig = $context->getScopeConfig();
        $this->_storeManager = $storeManager;
        $this->_configInterface = $configInterface;
    }

    /**
     * @param $path
     * @param $newValue
     * @param null $store
     * @return bool
     */
    public function updateConfigValue($path, $newValue, $store = null)
    {
        $websiteId = $this->_storeManager->getWebsite()->getId();
        if ($websiteId > 0) {
            $scope = \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE;
        } else {
            $scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT;
        }

        $value = $this->getConfigValue($path, $store);
        if ($value && ($value != $newValue)) {
            try {
                $this->_configInterface->saveConfig($path, $newValue, $scope, 0);
            } catch (\Exception $e) {

            }
        }

        return $value;
    }

    /**
     * @param $path
     * @param null $store
     * @return mixed
     */
    public function getConfigValue($path, $store = null)
    {
        return $this->_scopeConfig->getValue(
            $path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param $path
     * @param null $store
     * @return mixed
     */
    public function getConfigFlag($path, $store = null)
    {
        return $this->_scopeConfig->isSetFlag(
            $path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }
}