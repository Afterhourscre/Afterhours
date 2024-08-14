<?php


namespace MageCloud\RemoveDefaultStorePath\Plugin\Model;


class HideDefaultStoreCode
{
    /**
     *
     * @var \MageCloud\RemoveDefaultStorePath\Helper\Data
     */
    protected $helper;

    /**
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     *
     * @param \MageCloud\RemoveDefaultStorePath\Helper\Data $helper
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \MageCloud\RemoveDefaultStorePath\Helper\Data $helper,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ){
        $this->helper = $helper;
        $this->storeManager = $storeManager;
    }

    /**
     *
     * @param \Magento\Store\Model\Store $subject
     * @param string $url
     * @return string
     */
    public function afterGetBaseUrl(\Magento\Store\Model\Store $subject, $url)
    {
        if ($this->helper->isHideDefaultStoreCode()) {
            $url = str_replace('/'.$this->storeManager->getDefaultStoreView()->getCode().'/','/', $url);
        }
        return $url;
    }
}