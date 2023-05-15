<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Acr\Model;

use Aheadworks\Acr\Api\Data\CartRestoreInterface;
use Aheadworks\Acr\Api\Data\CartRestoreExtensionInterface;
use Aheadworks\Acr\Model\ResourceModel\CartRestore as CartRestoreResource;
use Magento\Framework\Model\AbstractModel;

/**
 * Class CartRestore
 * @package Aheadworks\Acr\Model
 */
class CartRestore extends AbstractModel implements CartRestoreInterface
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(CartRestoreResource::class);
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->getData(self::ENTITY_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setId($cartRestoreId)
    {
        return $this->setData(self::ENTITY_ID, $cartRestoreId);
    }

    /**
     * {@inheritdoc}
     */
    public function getEventHistoryId()
    {
        return $this->getData(self::EVENT_HISTORY_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setEventHistoryId($referenceId)
    {
        return $this->setData(self::EVENT_HISTORY_ID, $referenceId);
    }

    /**
     * {@inheritdoc}
     */
    public function getRestoreCode()
    {
        return $this->getData(self::RESTORE_CODE);
    }

    /**
     * {@inheritdoc}
     */
    public function setRestoreCode($restoreCode)
    {
        return $this->setData(self::RESTORE_CODE, $restoreCode);
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
    public function setExtensionAttributes(CartRestoreExtensionInterface $extensionAttributes)
    {
        return $this->setData(self::EXTENSION_ATTRIBUTES_KEY, $extensionAttributes);
    }
}
