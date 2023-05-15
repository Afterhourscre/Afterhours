<?php
/**
 * @author andy
 * @email andyworkbase@gmail.com
 * @team MageCloud
 */
namespace MageCloud\DeferJs\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

/**
 * Class Data
 * @package MageCloud\DeferJs\Helper
 */
class Data extends AbstractHelper
{
    /**
     * Parameters XML paths
     */
    CONST XML_PATH_DEFER_JS_ENABLED = 'defer_js/general/enabled';

    CONST XML_PATH_DEFER_JS_ALL_CONTROLLERS = 'defer_js/general/all_controllers';
    CONST XML_PATH_DEFER_JS_EXCLUDE_CONTROLLERS = 'defer_js/general/exclude_controllers';

    CONST XML_PATH_DEFER_JS_ALL_URL_PATHS = 'defer_js/general/all_url_paths';
    CONST XML_PATH_DEFER_JS_EXCLUDE_URL_PATHS = 'defer_js/general/exclude_url_paths';

    CONST XML_PATH_DEFER_JS_ALL_SCRIPTS = 'defer_js/general/all_scripts';
    CONST XML_PATH_DEFER_JS_EXCLUDE_SCRIPTS_ATTRIBUTE = 'defer_js/general/exclude_scripts_attribute';
    CONST XML_PATH_DEFER_JS_EXCLUDE_SCRIPTS_ATTRIBUTE_DEFAULT = 'data-defer-skip"';

    CONST XML_PATH_DEFER_JS_SPEED_TEST_GTMETRIX_API_ENDPOINT = 'defer_js/speed_test/gtmetrix_api_endpoint';
    CONST XML_PATH_DEFER_JS_SPEED_TEST_GTMETRIX_API_USERNAME = 'defer_js/speed_test/gtmetrix_api_username';
    CONST XML_PATH_DEFER_JS_SPEED_TEST_GTMETRIX_API_KEY = 'defer_js/speed_test/gtmetrix_api_key';

    CONST XML_PATH_DEFER_JS_SPEED_TEST_GOOGLE_API_ENDPOINT = 'defer_js/speed_test/google_page_speed_api_endpoint';
    CONST XML_PATH_DEFER_JS_SPEED_TEST_GOOGLE_API_KEY = 'defer_js/speed_test/google_page_speed_api_key';
    CONST XML_PATH_DEFER_JS_SPEED_TEST_GOOGLE_API_STRATEGY = 'defer_js/speed_test/google_page_speed_api_strategy';

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        parent::__construct($context);
        $this->_scopeConfig = $context->getScopeConfig();
        $this->_storeManager = $storeManager;
    }

    /**
     * @param null $store
     * @return mixed
     */
    public function isEnabled($store = null)
    {
        return $this->_scopeConfig->getValue(
            self::XML_PATH_DEFER_JS_ENABLED,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param $request
     * @param null $store
     * @return bool
     */
    public function checkAvailability($request, $store = null)
    {
        $enabled = $this->isEnabled($store);
        if ($enabled != 1) {
            return false;
        }

        $module = $request->getModuleName();
        $controller = $request->getControllerName();
        $action = $request->getActionName();

        if (!$this->isAllControllersEnabled($store) && $this->getExcludeControllers($store)) {
            if (
                $this->regexMatchSimple(
                    $this->getExcludeControllers($store), "{$module}_{$controller}_{$action}", 1
                )
            ) {
                return false;
            }
        }

        if (!$this->isAllUrlPathsEnabled($store) && $this->getExcludeUrlPaths($store)) {
                if (
                $this->regexMatchSimple(
                    $this->getExcludeUrlPaths($store), $request->getRequestUri(), 2
                )
            ) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param null $store
     * @return mixed
     */
    public function isAllControllersEnabled($store = null)
    {
        return $this->_scopeConfig->getValue(
            self::XML_PATH_DEFER_JS_ALL_CONTROLLERS,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param null $store
     * @return mixed
     */
    public function getExcludeControllers($store = null)
    {
        if (!$this->isAllControllersEnabled($store)) {
            return $this->_scopeConfig->getValue(
                self::XML_PATH_DEFER_JS_EXCLUDE_CONTROLLERS,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $store
            );
        }

        return false;
    }

    /**
     * @param null $store
     * @return mixed
     */
    public function isAllUrlPathsEnabled($store = null)
    {
        return $this->_scopeConfig->getValue(
            self::XML_PATH_DEFER_JS_ALL_URL_PATHS,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param null $store
     * @return bool|mixed
     */
    public function getExcludeUrlPaths($store = null)
    {
        if (!$this->isAllUrlPathsEnabled($store)) {
            return $this->_scopeConfig->getValue(
                self::XML_PATH_DEFER_JS_EXCLUDE_URL_PATHS,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $store
            );
        }

        return false;
    }

    /**
     * @param null $store
     * @return mixed
     */
    public function isAllScriptsEnabled($store = null)
    {
        return $this->_scopeConfig->getValue(
            self::XML_PATH_DEFER_JS_ALL_SCRIPTS,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param null $store
     * @return mixed
     */
    public function getExcludeScriptsAttribute($store = null)
    {
        if (!$this->isAllScriptsEnabled()) {
            $attribute = trim($this->_scopeConfig->getValue(
                self::XML_PATH_DEFER_JS_EXCLUDE_SCRIPTS_ATTRIBUTE,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $store
            ));

            return (strlen($attribute) > 0)
                ? $attribute
                : self::XML_PATH_DEFER_JS_EXCLUDE_SCRIPTS_ATTRIBUTE_DEFAULT;
        }

        return false;
    }

    /**
     * @param null $store
     * @return mixed
     */
    public function getGTMetrixApiEndpoint($store = null)
    {
        return $this->_scopeConfig->getValue(
            self::XML_PATH_DEFER_JS_SPEED_TEST_GTMETRIX_API_ENDPOINT,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param null $store
     * @return mixed
     */
    public function getGTMetrixApiUserName($store = null)
    {
        return $this->_scopeConfig->getValue(
            self::XML_PATH_DEFER_JS_SPEED_TEST_GTMETRIX_API_USERNAME,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param null $store
     * @return mixed
     */
    public function getGTMetrixApiKey($store = null)
    {
        return $this->_scopeConfig->getValue(
            self::XML_PATH_DEFER_JS_SPEED_TEST_GTMETRIX_API_KEY,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param null $store
     * @return mixed
     */
    public function getGooglePageSpeedApiEndpoint($store = null)
    {
        return $this->_scopeConfig->getValue(
            self::XML_PATH_DEFER_JS_SPEED_TEST_GOOGLE_API_ENDPOINT,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param null $store
     * @return mixed
     */
    public function getGooglePageSpeedApiKey($store = null)
    {
        return $this->_scopeConfig->getValue(
            self::XML_PATH_DEFER_JS_SPEED_TEST_GOOGLE_API_KEY,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param null $store
     * @return mixed
     */
    public function getGooglePageSpeedApiStrategy($store = null)
    {
        return $this->_scopeConfig->getValue(
            self::XML_PATH_DEFER_JS_SPEED_TEST_GOOGLE_API_STRATEGY,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param $regex
     * @param $matchTerm
     * @param $type
     * @return bool
     */
    public function regexMatchSimple($regex, $matchTerm, $type)
    {
        if (!$regex) {
            return false;
        }

        $rules = @unserialize($regex);
        if (empty($rules)) {
            return false;
        }

        foreach ($rules as $rule) {
            $regex = trim($rule['defer'], '#');
            if ($regex == '') {
                continue;
            }

            if ($type == 1) {
                $regexs = explode('_', $regex);
                switch (count($regexs)) {
                    case 1:
                        $regex = $regex . '_index_index';
                        break;
                    case 2:
                        $regex = $regex . '_index';
                        break;
                    default:
                        break;
                }
            }
            $regexp = '#' . $regex . '#';
            if (@preg_match($regexp, $matchTerm)) {
                return true;
            }
        }

        return false;
    }
}