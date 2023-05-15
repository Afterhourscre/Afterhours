<?php
/**
 * @author andy
 * @email andyworkbase@gmail.com
 * @team MageCloud
 */
namespace MageCloud\DeferJs\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\Http\Context;
use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class DeferJs
 * @package MageCloud\DeferJS\Observer
 */
class DeferJs implements ObserverInterface
{
    /**
     * @var \MageCloud\DeferJs\Helper\Data
     */
    protected $_helper;

    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * DeferJs constructor.
     * @param \MageCloud\DeferJs\Helper\Data $helper
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        \MageCloud\DeferJs\Helper\Data $helper,
        StoreManagerInterface $storeManager
    ) {
        $this->_helper = $helper;
        $this->_storeManager = $storeManager;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this|bool
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Framework\App\RequestInterface $request */
        $request = $observer->getEvent()->getData('request');
        if (!$this->_helper->checkAvailability($request)) {
            return false;
        }

        /** @var \Magento\Framework\App\ResponseInterface $response */
        $response = $observer->getEvent()->getData('response');
        if (!$response) {
            return false;
        }

        $html = $response->getBody();
        if (($html == '') || (stripos($html, '</body>') === false)) {
            return false;
        }

        $pattern = '~<\s*\bscript\b[^>]*>(.*?)<\s*\/\s*script\s*>~is';
        if (!$this->_helper->isAllScriptsEnabled()) {
            $attribute = $this->_helper->getExcludeScriptsAttribute();
            if ($attribute) {
                $pattern = '@(?!<script type="text/javascript" ' . $attribute . ' |<script ' . $attribute . ')(' . $pattern . ')@msU';
            }
        }

        preg_match_all($pattern, $html, $scripts);
        if ($scripts and isset($scripts[0]) and $scripts[0]) {
            $html = preg_replace($pattern, '', $response->getBody());
            $scripts = implode('', $scripts[0]);
            $html = str_ireplace("</body>", "$scripts</body>", $html);
            $response->setBody($html);
        }

        return $this;
    }
}