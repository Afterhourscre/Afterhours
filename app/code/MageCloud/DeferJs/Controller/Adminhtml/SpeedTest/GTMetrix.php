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
 * Class GTMetrix
 * @package MageCloud\DeferJs\Controller\Adminhtml\SpeedTest
 */
class GTMetrix extends SpeedTest
{
    /**
     * @var string
     */
    protected $_apiEndpoint;

    /**
     * @var string
     */
    protected $_apiUserName;

    /**
     * @var string
     */
    protected $_apiKey;

    /**
     * @return mixed|string
     */
    public function getApiEndpoint()
    {
        if (!$this->_apiEndpoint) {
            $apiEndpointFromDb = $this->_dataHelper->getGTMetrixApiEndpoint();
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
    public function getApiUserName()
    {
        if (!$this->_apiUserName) {
            $apiUserNameFromDb = $this->_dataHelper->getGTMetrixApiUserName();
            if (strlen($apiUserNameFromDb) > 0) {
                // set api username from db
                $this->_apiUserName = $apiUserNameFromDb;
            } else {
                // set api username from request
                $this->_apiUserName = $this->getRequest()->getParam('api_username');
            }
        }
        return $this->_apiUserName;
    }

    /**
     * @return mixed|string
     */
    public function getApiKey()
    {
        if (!$this->_apiKey) {
            $apiKeyFromDb = $this->_dataHelper->getGTMetrixApiKey();
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
     * @return bool
     */
    public function execute()
    {
        $checkRequest = $this->_checkRequest($this->getRequest());
        if ($checkRequest != '') {
            $this->getResponse()->setBody($checkRequest);
        }

        $client = new GTMetrixClient();
        $client->setUsername($this->getApiUserName());
        $client->setAPIKey($this->getApiKey());

        $client->getLocations();
        $client->getBrowsers();
        $processTest = $client->startTest($this->getBaseUrl());

        // wait for result
        while (
            $processTest->getState()
            != GTMetrixTest::STATE_COMPLETED
            && $processTest->getState() != GTMetrixTest::STATE_ERROR
        ) {
            $client->getTestStatus($processTest);
            sleep(5);
        }

        // convert and format response data
		$responseData = (array)$this->objectToArray($processTest);
        //@TODO: move response to separate template
		$responseContent = $this->_buildResponse($responseData);
        $this->getResponse()->setBody(
            $responseContent
                ? $responseContent
                    : __('Test return empty results. Please try again.')
        );
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

            if (!$this->getApiEndpoint() || !$this->getApiUserName() || !$this->getApiKey()) {
                $response = _('API Endpoint, API Username and API Key must be set up before using API calls!');
            };
        }

        return $response;
    }

    /**
     * @TODO: move response to separate template
     *
     * @param $responseData
     * @return bool|string
     */
    protected function _buildResponse($responseData)
    {
        if (!$responseData) {
            return false;
        }

        $resultData = [];
        foreach ($responseData as $param => $value) {
            $formatParam = preg_replace('/[^a-z]/i', "", $param);
            $resultData[$formatParam] = $value;
        }

        $outputData = '';
        $outputData .= '<table class="speed-test-results">';
        $outputData .= '<tbody>';
        foreach ($resultData as $param => &$value) {
            if (!is_array($value)) {
                // format response data for better ui
                if (isset($resultData['error']) && empty($resultData['error'])) {
                    unset($resultData['error']);
                }
                // separate param words, make it first letter uppercase
                $formatResourceParam = ucfirst(preg_replace("/(?<=[a-zA-Z])(?=[A-Z])/", " ", $param));
                $formatValue = (strpos($value, 'http') === 0)
                    ? '<a href="' . $value . '" target="_blank">' . $value .'</a>'
                    : $value;
                $outputData .= '<tr>';
                    $outputData .= '<th>' . $formatResourceParam . '</th>';
                    $outputData .= '<td>' . $formatValue . '</td>';
                $outputData .= '</tr>';
            } else {
                foreach ($value as $resourceName => $url) {
                    $formatResourceName = ucfirst(str_replace('_', ' ', $resourceName));
                    $outputData .= '<tr>';
                        $outputData .= '<th>' . $formatResourceName . '</th>';
                        $outputData .= '<td>' . '<a href="' . $url . '" target="_blank">' . $url . '</a>' . '</td>';
                    $outputData .= '</tr>';
                }
            }
        }

        $outputData .= '</tbody>';
        $outputData .= '</table>';
        $outputData .= "<br/>";

        $outputData .= '<div class="messages">';
            $outputData .= '<div class="message message-notice notice" style="background-color: #fffbbb">';
                $outputData .= '<div>';
                    $outputData .= __('Please Note: the test ID is only valid for 3 days.
                        The current GTmetrix report will be valid for 30 days.');
                $outputData .= '</div>';
            $outputData .= '</div>';
        $outputData .= '</div>';

        return (strlen($outputData) > 0) ? $outputData : false;
    }
}