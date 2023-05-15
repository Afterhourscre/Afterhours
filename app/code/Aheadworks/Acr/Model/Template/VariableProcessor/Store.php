<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Acr\Model\Template\VariableProcessor;

use Magento\Store\Model\StoreManagerInterface;
use Aheadworks\Acr\Model\Source\Email\Variables;

/**
 * Class Store
 *
 * @package Aheadworks\Acr\Model\Template\VariableProcessor
 */
class Store implements VariableProcessorInterface
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        StoreManagerInterface $storeManager
    ) {
        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function process($quote, $params)
    {
        return [Variables::STORE => $this->storeManager->getStore($quote->getCustomer()->getStoreId())];
    }

    /**
     * {@inheritdoc}
     */
    public function processTest($params)
    {
        return [Variables::STORE => $this->storeManager->getStore($params['store_id'])];
    }
}
