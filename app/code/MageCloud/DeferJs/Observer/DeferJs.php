<?php
namespace MageCloud\DeferJs\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\Http\Context;
use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

class DeferJs implements ObserverInterface
{
    protected $_helper;
    protected $_storeManager;
    protected $_logger;

    public function __construct(
        \MageCloud\DeferJs\Helper\Data $helper,
        StoreManagerInterface $storeManager,
        LoggerInterface $logger
    ) {
        $this->_helper = $helper;
        $this->_storeManager = $storeManager;
        $this->_logger = $logger;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $this->_logger->debug('DeferJs Observer Execution Started');

        $request = $observer->getEvent()->getData('request');
        if (!$this->_helper->checkAvailability($request)) {
            $this->_logger->debug('DeferJs: Helper check availability failed');
            return false;
        }

        $response = $observer->getEvent()->getData('response');
        if (!$response) {
            $this->_logger->debug('DeferJs: Response is not available');
            return false;
        }

        $html = $response->getBody();
        $this->_logger->debug('DeferJs: Response body retrieved');

        if (($html == '') || (stripos($html, '</body>') === false)) {
            $this->_logger->debug('DeferJs: Response body is empty or does not contain </body>');
            return false;
        }

        $pattern = '~<\s*\bscript\b[^>]*>(.*?)<\s*\/\s*script\s*>~is';
        if (!$this->_helper->isAllScriptsEnabled()) {
            $attribute = $this->_helper->getExcludeScriptsAttribute();
            if ($attribute) {
                $pattern = '@(?!<script type="text/javascript" ' . $attribute . ' |<script ' . $attribute . ')(' . $pattern . ')@msU';
                $this->_logger->debug('DeferJs: Pattern updated with attribute exclusion');
            }
        }

        preg_match_all($pattern, $html, $scripts);
        if ($scripts and isset($scripts[0]) and $scripts[0]) {
            $this->_logger->debug('DeferJs: Scripts found and extracted');
            $html = preg_replace($pattern, '', $html);
            $scripts = implode('', $scripts[0]);
            if ($html !== null) {
                $html = str_ireplace("</body>", "$scripts</body>", $html);
                $response->setBody($html);
                $this->_logger->debug('DeferJs: Response body updated with deferred scripts');
            } else {
                $this->_logger->debug('DeferJs: HTML content is null');
            }
        } else {
            $this->_logger->debug('DeferJs: No scripts found to defer');
        }

        $this->_logger->debug('DeferJs Observer Execution Ended');
        return $this;
    }
}
