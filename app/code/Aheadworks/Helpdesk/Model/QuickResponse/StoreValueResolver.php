<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Helpdesk\Model\QuickResponse;

use Aheadworks\Helpdesk\Api\Data\StoreValueInterface;
use Magento\Store\Model\Store;
use Magento\Framework\Reflection\DataObjectProcessor;
use Aheadworks\Helpdesk\Model\StoreValue\ObjectResolver as StoreValueObjectResolver;

/**
 * Class StoreValueResolver
 * @package Aheadworks\Helpdesk\Model\QuickResponse
 */
class StoreValueResolver
{
    /**
     * @var DataObjectProcessor
     */
    private $dataObjectProcessor;

    /**
     * @var StoreValueObjectResolver
     */
    private $objectResolver;

    /**
     * @param StoreValueObjectResolver $objectResolver
     * @param DataObjectProcessor $dataObjectProcessor
     */
    public function __construct(
        StoreValueObjectResolver $objectResolver,
        DataObjectProcessor $dataObjectProcessor
    ) {
        $this->objectResolver = $objectResolver;
        $this->dataObjectProcessor = $dataObjectProcessor;
    }

    /**
     * Get value by store ID
     *
     * @param StoreValueInterface[]|array $storeValueItems
     * @param $storeId
     * @return string|null
     */
    public function getValueByStoreId($storeValueItems, $storeId)
    {
        $storeValue = null;
        $defaultValue = null;

        foreach ($storeValueItems as $storeValueItem) {
            $storeValueObject = $this->objectResolver->resolve($storeValueItem);
            if ($storeValueObject->getStoreId() == $storeId) {
                $storeValue = $storeValueObject->getValue();
                break;
            }
            if ($storeValueObject->getStoreId() == Store::DEFAULT_STORE_ID) {
                $defaultValue = $storeValueObject->getValue();
            }
        }
        return $storeValue ? $storeValue : $defaultValue;
    }
}
