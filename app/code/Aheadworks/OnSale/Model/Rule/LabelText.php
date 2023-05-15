<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Model\Rule;

use Aheadworks\OnSale\Api\Data\LabelTextInterface;
use Magento\Framework\Api\AbstractExtensibleObject;

/**
 * Class LabelText
 *
 * @package Aheadworks\OnSale\Model\Rule
 */
class LabelText extends AbstractExtensibleObject implements LabelTextInterface
{
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
