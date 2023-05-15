<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Model\Rule\LabelText\StoreValue;

use Aheadworks\OnSale\Api\Data\LabelTextStoreValueInterface;
use Aheadworks\OnSale\Api\Data\LabelTextStoreValueInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;

/**
 * Class ObjectResolver
 *
 * @package Aheadworks\OnSale\Model\StoreValue
 */
class ObjectResolver
{
    /**
     * @var LabelTextStoreValueInterfaceFactory
     */
    private $storeValueFactory;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @param LabelTextStoreValueInterfaceFactory $storeValueFactory
     * @param DataObjectHelper $dataObjectHelper
     */
    public function __construct(
        LabelTextStoreValueInterfaceFactory $storeValueFactory,
        DataObjectHelper $dataObjectHelper
    ) {
        $this->storeValueFactory = $storeValueFactory;
        $this->dataObjectHelper = $dataObjectHelper;
    }

    /**
     * Resolve row storeValue
     *
     * @param LabelTextStoreValueInterface[]|array $storeValueItem
     * @return LabelTextStoreValueInterface
     */
    public function resolve($storeValueItem)
    {
        if ($storeValueItem instanceof LabelTextStoreValueInterface) {
            $storeValueObject = $storeValueItem;
        } else {
            /** @var LabelTextStoreValueInterface $labelObject */
            $storeValueObject = $this->storeValueFactory->create();
            $this->dataObjectHelper->populateWithArray(
                $storeValueObject,
                $storeValueItem,
                LabelTextStoreValueInterface::class
            );
        }
        return $storeValueObject;
    }
}
