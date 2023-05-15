<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Model\Rule\LabelText;

use Aheadworks\OnSale\Api\Data\LabelTextStoreValueInterface;
use Magento\Framework\Api\AbstractExtensibleObject;

/**
 * Class StoreValue
 *
 * @package Aheadworks\OnSale\Model\Rule\LabelText
 */
class StoreValue extends AbstractExtensibleObject implements LabelTextStoreValueInterface
{
    /**
     * {@inheritdoc}
     */
    public function getStoreId()
    {
        return $this->_get(self::STORE_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setStoreId($storeId)
    {
        return $this->setData(self::STORE_ID, $storeId);
    }

    /**
     * {@inheritdoc}
     */
    public function getValueLarge()
    {
        return $this->_get(self::VALUE_LARGE);
    }

    /**
     * {@inheritdoc}
     */
    public function setValueLarge($valueLarge)
    {
        return $this->setData(self::VALUE_LARGE, $valueLarge);
    }

    /**
     * {@inheritdoc}
     */
    public function getValueMedium()
    {
        return $this->_get(self::VALUE_MEDIUM);
    }

    /**
     * {@inheritdoc}
     */
    public function setValueMedium($valueMedium)
    {
        return $this->setData(self::VALUE_MEDIUM, $valueMedium);
    }

    /**
     * {@inheritdoc}
     */
    public function getValueSmall()
    {
        return $this->_get(self::VALUE_SMALL);
    }

    /**
     * {@inheritdoc}
     */
    public function setValueSmall($valueSmall)
    {
        return $this->setData(self::VALUE_SMALL, $valueSmall);
    }
}
