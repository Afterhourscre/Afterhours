<?php

namespace MageCloud\StoreSwitcher\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

class Data extends AbstractHelper
{

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        parent::__construct($context);
        $this->_storeManager = $storeManager;
    }

    /**
     * @return \Magento\Store\Model\Store[]
     */
    public function getAllStores()
    {
        $stores = [];
        $allStores = $this->_storeManager->getStores();
        foreach ($allStores as $store) {
            /* @var $store \Magento\Store\Model\Store */
            if (!$store->isActive()) {
                continue;
            }

//            $localeCode = $this->_scopeConfig->getValue(
//                \Magento\Directory\Helper\Data::XML_PATH_DEFAULT_LOCALE,
//                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
//                $store
//            );
//            $store->setLocaleCode($localeCode);
//            $params = ['_query' => []];
//            if (!$this->isStoreInUrl()) {
//                $params['_query']['___store'] = $store->getCode();
//            }
//            $baseUrl = $store->getUrl('', $params);
//
//            $store->setHomeUrl($baseUrl);
//            $stores[$store->getGroupId()][$store->getId()] = $store;

            $stores[] = $store;
        }

        return $stores;
    }

}
