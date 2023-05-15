<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Model\Rule\LabelText;

use Aheadworks\OnSale\Api\Data\LabelTextStoreValueInterface;
use Magento\Store\Model\Store;
use Aheadworks\OnSale\Model\Rule\LabelText\StoreValue\ObjectResolver as StoreValueObjectResolver;
use Aheadworks\OnSale\Model\Rule\LabelText\StoreResolver\Converter;
use Aheadworks\OnSale\Api\Data\LabelTextInterface;
use Aheadworks\OnSale\Api\Data\LabelTextInterfaceFactory;

/**
 * Class StoreResolver
 *
 * @package Aheadworks\OnSale\Model\Rule\LabelText
 */
class StoreResolver
{
    /**
     * @var StoreValueObjectResolver
     */
    protected $objectResolver;

    /**
     * @var Converter
     */
    protected $converter;

    /**
     * @param StoreValueObjectResolver $objectResolver
     * @param Converter $converter
     */
    public function __construct(
        StoreValueObjectResolver $objectResolver,
        Converter $converter
    ) {
        $this->objectResolver = $objectResolver;
        $this->converter = $converter;
    }

    /**
     * Get store value data by store ID
     *
     * @param LabelTextStoreValueInterface[]|array $storeValueItems
     * @param int $storeId
     * @return LabelTextStoreValueInterface|null
     */
    public function getValueByStoreId($storeValueItems, $storeId)
    {
        $storeValue = null;
        $defaultValue = null;

        foreach ($storeValueItems as $storeValueItem) {
            $storeValueObject = $this->objectResolver->resolve($storeValueItem);
            if ($storeValueObject->getStoreId() == $storeId) {
                $storeValue = $storeValueObject;
                break;
            }
            if ($storeValueObject->getStoreId() == Store::DEFAULT_STORE_ID) {
                $defaultValue = $storeValueObject;
            }
        }

        return $storeValue ? $storeValue : $defaultValue;
    }

    /**
     * Get label text data by store ID as array
     *
     * @param LabelTextStoreValueInterface[]|array $storeValueItems
     * @param int $storeId
     * @return LabelTextInterface|null
     */
    public function getLabelTextAsObject($storeValueItems, $storeId)
    {
        $storeValueObject = $this->getValueByStoreId($storeValueItems, $storeId);
        return $storeValueObject
            ? $this->converter->convertToLabelTextObject($storeValueObject)
            : null;
    }

    /**
     * Get label text data by store ID as array
     *
     * @param LabelTextStoreValueInterface[]|array $storeValueItems
     * @param int $storeId
     * @return array|null
     */
    public function getLabelTextAsArray($storeValueItems, $storeId)
    {
        $storeValueObject = $this->getValueByStoreId($storeValueItems, $storeId);
        return $storeValueObject
            ? $this->converter->convertToLabelTextArray($storeValueObject)
            : null;
    }
}
