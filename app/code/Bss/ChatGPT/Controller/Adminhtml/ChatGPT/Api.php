<?php
/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * @category   BSS
 * @package    Bss_ChatGPT
 * @author     Extension Team
 * @copyright  Copyright (c) 2023-2024 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */

namespace Bss\ChatGPT\Controller\Adminhtml\ChatGPT;

use Bss\ChatGPT\Model\APIChatGPT;
use Bss\ChatGPT\Model\Config;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Serialize\Serializer\Json;

class Api extends \Magento\Backend\App\Action implements \Magento\Framework\App\Action\HttpPostActionInterface
{
    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var \Bss\ChatGPT\Model\Config
     */
    protected $config;

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    protected $json;

    /**
     * @var APIChatGPT
     */
    protected $apiChatGPT;

    /**
     * @param APIChatGPT $apiChatGPT
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param Config $config
     * @param Json $json
     */
    public function __construct(
        APIChatGPT                                       $apiChatGPT,
        Context                                          $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Bss\ChatGPT\Model\Config                        $config,
        \Magento\Framework\Serialize\Serializer\Json     $json
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->config = $config;
        $this->json = $json;
        $this->apiChatGPT = $apiChatGPT;
    }

    /**
     * API execute.
     *
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        $request = $this->getRequest()->getParams() ?? [];
        $content = [
            "success" => "",
            "error" => ""
        ];

        if (!$this->config->isEnable()) {
            $content["error"] = __("Module ChatGPT is disabled!");
            return $this->contentToJson($content);
        }

        if (isset($request['search'])) {
            $search = (string)$request['search'];

            // Remove '{{', '}}' before call API.
            $search = str_replace("{{", " ", $search);
            $search = str_replace("}}", " ", $search);

            $dataApi['system_role'] = $request['system_role'] ?? "";
            $dataApi['prompt'] = $search;
        } else {
            $content["error"] = __("Request prompt null!");
            return $this->contentToJson($content);
        }

        if (empty($request['test_key'])) {
            /* Config general */
            $this->getConfigApi($dataApi);
        } else {
            /* Call ChatGPT Test */
            $this->getConfigApiTest($dataApi, $request);
        }

        $content = $this->apiChatGPT->callChatGPT($dataApi, $content);

        return $this->contentToJson($content);
    }

    /**
     * Create json factory.
     *
     * @param array $content
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function contentToJson($content)
    {
        $response = ['data' => $content];
        $resultJson = $this->resultJsonFactory->create();
        $resultJson->setData($response);

        return $resultJson;
    }

    /**
     * Get all config
     *
     * @param array $result
     * @return array
     */
    public function getConfigApi(&$result)
    {
        $result['url'] = $this->config->getApiUrl();
        $result['api_key'] = $this->config->getApiKey();
        $result['model'] = $this->config->getModelType();
        $result['temperature'] = $this->config->getTemperature();
        $result['max_tokens'] = $this->config->getMaxTokens();

        return $result;
    }

    /**
     * Get config test
     *
     * @param array $result
     * @param array $request
     * @return array
     */
    public function getConfigApiTest(&$result, $request)
    {
        $result['url'] = $this->config->getApiUrl();
        $result['api_key'] = (int)$request['changed_key']
            ? (string)$request['api_key']
            : $this->config->getApiKey();
        $result['model'] = (string)$request['model'];
        $result['temperature'] = (float)$request['temperature'];
        $result['max_tokens'] = (int)$request['max_tokens'];
        $result['system_role'] = (string)$request['system_role'];

        return $result;
    }
}
