<?php
/**
 * @author andy
 * @email andyworkbase@gmail.com
 * @team MageCloud
 */
namespace MageCloud\DeferJs\Controller\Adminhtml\SpeedTest;

use MageCloud\DeferJs\Controller\Adminhtml\SpeedTest;
use Magento\Framework\App\RequestInterface;
use MageCloud\DeferJs\Helper\Lib\GTMetrix\GTMetrixClient;
use MageCloud\DeferJs\Helper\Lib\GTMetrix\GTMetrixTest;

/**
 * Class GooglePageSpeed
 * @package MageCloud\DeferJs\Controller\Adminhtml\SpeedTest
 */
class GooglePageSpeed extends SpeedTest
{
    /**
     * @var string
     */
    protected $_apiEndpoint;

    /**
     * @var string
     */
    protected $_apiKey;

    /**
     * @var string
     */
    protected $_apiStrategy;

    /**
     * @return mixed|string
     */
    public function getApiEndpoint()
    {
        if (!$this->_apiEndpoint) {
            $apiEndpointFromDb = $this->_dataHelper->getGooglePageSpeedApiEndpoint();
            if (strlen($apiEndpointFromDb) > 0) {
                // set api endpoint from db
                $this->_apiEndpoint = $apiEndpointFromDb;
            } else {
                // set api endpoint from request
                $this->_apiEndpoint = $this->getRequest()->getParam('api_endpoint');
            }
        }
        return $this->_apiEndpoint;
    }

    /**
     * @return mixed|string
     */
    public function getApiKey()
    {
        if (!$this->_apiKey) {
            $apiKeyFromDb = $this->_dataHelper->getGooglePageSpeedApiKey();
            if (strlen($apiKeyFromDb) > 0) {
                // set api key from db
                $this->_apiKey = $apiKeyFromDb;
            } else {
                // set api key from request
                $this->_apiKey = $this->getRequest()->getParam('api_key');
            }
        }
        return $this->_apiKey;
    }

    /**
     * @return mixed|string
     */
    public function getApiStrategy()
    {
        if (!$this->_apiStrategy) {
            $apiKeyFromDb = $this->_dataHelper->getGooglePageSpeedApiStrategy();
            if (strlen($apiKeyFromDb) > 0) {
                // set api key from db
                $this->_apiStrategy = $apiKeyFromDb;
            } else {
                // set api key from request
                $this->_apiStrategy = $this->getRequest()->getParam('api_strategy');
            }
        }
        return $this->_apiStrategy;
    }

    /**
     * @return bool
     */
    public function execute()
    {
        $checkRequest = $this->_checkRequest($this->getRequest());
        if ($checkRequest != '') {
            $this->getResponse()->setBody($checkRequest);
        }

        $url = $this->_buildUrl();

        $curl = $this->_curl;
        $curl->addOption(CURLOPT_URL, $url);
        $curl->addOption(CURLOPT_RETURNTRANSFER, true);
        $curl->addOption(CURLOPT_SSL_VERIFYPEER, false);

        $curl->connect($url);
        $response = $curl->read();
        $responseCode = $curl->getInfo(CURLINFO_HTTP_CODE);
        $curl->close();
        $httpSuccessResponseCode = 200;

        if ($responseCode == $httpSuccessResponseCode) {
            $this->getResponse()->setBody($response);
        }
    }

    /**
     * @param array $additionalParams
     * @return string
     */
    protected function _buildUrl($additionalParams = [])
    {
        $params = [
            'url' => $this->getBaseUrl(),
            'strategy' => $this->getApiStrategy(),
            'screenshot' => 'true',
            'key' => $this->getApiKey()
        ];

        if (!empty($additionalParams)) {
            $params = array_merge($params, $additionalParams);
        };

        $url = $this->getApiEndpoint() . '?' . urldecode(http_build_query($params));

        return $url;
    }

    /**
     * @param $request
     * @return mixed
     */
    protected function _checkRequest($request)
    {
        $response = '';
        if ($request instanceof RequestInterface) {
            if (!$this->_formKeyValidator->validate($request)) {
                $response = _('Invalid form key. Please refresh the page.');
            };

            if (!$this->getApiKey() || !$this->getApiEndpoint()) {
                $response = _('API Endpoint and API Key must be set up before using API calls!');
            };
        }

        return $response;
    }
}