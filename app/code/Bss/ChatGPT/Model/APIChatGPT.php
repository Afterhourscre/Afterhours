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
 * @copyright  Copyright (c) 2024 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */

namespace Bss\ChatGPT\Model;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

class APIChatGPT extends AbstractHelper
{
    /**
     * Number of "tokens" safe to not exceed ChatGPT.
     */
    public const CHATGPT_TOKENS_SAFE = 10;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var Curl
     */
    protected $curl;

    /**
     * @var Json
     */
    protected $json;

    /**
     * @param Curl $curl
     * @param Json $json
     * @param LoggerInterface $logger
     * @param StoreManagerInterface $storeManager
     * @param Context $context
     */
    public function __construct(
        \Magento\Framework\HTTP\Client\Curl $curl,
        \Magento\Framework\Serialize\Serializer\Json $json,
        \Psr\Log\LoggerInterface $logger,
        StoreManagerInterface $storeManager,
        Context $context)
    {
        $this->curl = $curl;
        $this->json = $json;
        $this->logger = $logger;
        $this->storeManager = $storeManager;
        parent::__construct($context);
    }

    /**
     * @return mixed
     */
    public function getDefaultStoreId()
    {
        return $this->_getRequest()->getParam('store', \Magento\Store\Model\Store::DEFAULT_STORE_ID);
    }

    /**
     * @param $storeId
     * @return int
     * @throws NoSuchEntityException
     */
    public function getWebsiteId($storeId = null)
    {
        return $this->storeManager->getStore($storeId ?? $this->getDefaultStoreId())->getWebsiteId();
    }

    /**
     * @param $tokenConfig
     * @param $request
     * @param $content
     * @return float|int
     */
    public function calculateMaxTokens($tokenConfig, $request, &$content)
    {
        if (!$request) {
            return 0;
        }

        // 1 token generally corresponds to ~4 characters of text for common English text.
        $promptTokens = round(strlen($request) / 4);
        $tokens = $tokenConfig - $promptTokens - self::CHATGPT_TOKENS_SAFE;

        if ($tokens < self::CHATGPT_TOKENS_SAFE) {
            $this->logger->error(__("Config Max Tokens (API ChatGPT) is too low."));
            $content["error"] = __("Config Max Tokens (API ChatGPT) is too low.");
            return 0;
        }

        return $tokens;
    }

    /**
     * @param $request
     * @param $maxTokens
     * @return bool|string
     */
    protected function getPayload($request, $maxTokens)
    {
        return $this->json->serialize([
            "messages" => [
                [
                    'role' => 'system',
                    'content' => $request['system_role']
                ],
                [
                    'role' => 'user',
                    'content' => $request['prompt']
                ]
            ],
            "model" => $request['model'],
            "temperature" => $request['temperature'],
            "max_tokens" => $maxTokens
        ]);
    }

    /**
     * @param $request
     * @param $payload
     * @return array|bool|float|int|mixed|string|null
     */
    protected function callApi($request, $payload)
    {
        $this->curl->addHeader("Content-Type", "application/json");
        $this->curl->addHeader("Authorization", "Bearer " . $request['api_key']);
        $this->curl->setOptions(
            [
                CURLOPT_URL => $request['url'],
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => $payload
            ]
        );
        $this->curl->post($request['url'], []);
        $response = $this->curl->getBody();
        return $this->json->unserialize($response);
    }

    /**
     * @param $contentAI
     * @return string
     */
    public function prepareContent($contentAI)
    {
        $this->removeCharacterFromContent($contentAI);
        return trim($contentAI);
    }

    /**
     * Remove character from content
     *
     * @param $contentAI
     * @param $character
     * @return void
     */
    public function removeCharacterFromContent(&$contentAI)
    {
        $contentAI = str_replace('```html', "", $contentAI);
        $contentAI = str_replace('```', "", $contentAI);
        $contentAI = str_replace('`', "", $contentAI);
    }

    /**
     * @param $result
     * @return string
     */
    protected function getContentAI($result)
    {
        $contentAI = "";
        if (isset($result['choices'][0]['message']['content'])) {
            /* Validate content AI */
            $contentAI = preg_replace(
                '/^[\s\r\n]*(\'|"|\s)+|(\'|"|\s)+$/',
                '',
                $result['choices'][0]['message']['content']
            );
            $contentAI = $this->prepareContent($contentAI);
        }
        return $contentAI;
    }

    /**
     * @param $request
     * @param $content
     * @return mixed|string[]
     */
    public function callChatGPT($request, $content)
    {
        if (!$content){
            $content = [
                "success" => "",
                "error" => ""
            ];
        }
        try {
            $maxTokens = $this->calculateMaxTokens(
                (int)$request['max_tokens'],
                $request['system_role'] . $request['prompt'],
                $content
            );

            if (!$maxTokens) {
                return $content;
            }

            $payload = $this->getPayload($request, $maxTokens);

            $result = $this->callApi($request, $payload);

            if (isset($result['error']['message'])) {
                $errMessChatGPT = $result['error']['message'] . " " . $result['error']['code'] ?? "";
                $this->logger->error($errMessChatGPT);
                $content["error"] = $errMessChatGPT;
            }

            $content["success"] = $this->getContentAI($result);
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            $content["error"] = $e->getMessage();
        }
        return $content;
    }
}
