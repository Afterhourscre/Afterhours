<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Acr\Model;

use Aheadworks\Acr\Api\Data\CartHistoryInterface;
use Aheadworks\Acr\Api\Data\CartHistoryExtensionInterface;
use Aheadworks\Acr\Model\ResourceModel\CartHistory as CartHistoryResource;
use Magento\Framework\Model\AbstractModel;

/**
 * Class CartHistory
 * @package Aheadworks\Acr\Model
 */
class CartHistory extends AbstractModel implements CartHistoryInterface
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(CartHistoryResource::class);
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->getData(self::ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setId($cartHistoryId)
    {
        return $this->setData(self::ID, $cartHistoryId);
    }

    /**
     * {@inheritdoc}
     */
    public function getReferenceId()
    {
        return $this->getData(self::REFERENCE_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setReferenceId($referenceId)
    {
        return $this->setData(self::REFERENCE_ID, $referenceId);
    }

    /**
     * {@inheritdoc}
     */
    public function getCartData()
    {
        return $this->getData(self::CART_DATA);
    }

    /**
     * {@inheritdoc}
     */
    public function setCartData($cartData)
    {
        return $this->setData(self::CART_DATA, $cartData);
    }

    /**
     * {@inheritdoc}
     */
    public function getTriggeredAt()
    {
        return $this->getData(self::TRIGGERED_AT);
    }

    /**
     * {@inheritdoc}
     */
    public function setTriggeredAt($triggeredAt)
    {
        return $this->setData(self::TRIGGERED_AT, $triggeredAt);
    }

    /**
     * {@inheritdoc}
     */
    public function getProcessed()
    {
        return $this->getData(self::PROCESSED);
    }

    /**
     * {@inheritdoc}
     */
    public function setProcessed($processed)
    {
        return $this->setData(self::PROCESSED, $processed);
    }

    /**
     * {@inheritdoc}
     */
    public function getExtensionAttributes()
    {
        return $this->getData(self::EXTENSION_ATTRIBUTES_KEY);
    }

    /**
     * {@inheritdoc}
     */
    public function setExtensionAttributes(CartHistoryExtensionInterface $extensionAttributes)
    {
        return $this->setData(self::EXTENSION_ATTRIBUTES_KEY, $extensionAttributes);
    }
}
