<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Model\Rule\LabelText\StoreResolver;

use Aheadworks\OnSale\Api\Data\LabelTextStoreValueInterface;
use Magento\Store\Model\Store;
use Aheadworks\OnSale\Model\Rule\LabelText\StoreResolver;

/**
 * Class Backend
 *
 * @package Aheadworks\OnSale\Model\Rule\LabelText\StoreResolver
 */
class Backend extends StoreResolver
{
    /**
     * Get value by store ID
     *
     * @param LabelTextStoreValueInterface[]|array $storeValueItems
     * @param int $storeId
     * @return string|null
     */
    public function getValueByStoreId($storeValueItems, $storeId)
    {
        $backendValue = null;
        $anyValue = null;

        foreach ($storeValueItems as $storeValueItem) {
            $storeValueObject = $this->objectResolver->resolve($storeValueItem);
            if ($storeValueObject->getStoreId() == Store::DEFAULT_STORE_ID) {
                $backendValue = $storeValueObject;
                break;
            } else {
                $anyValue = $storeValueObject;
            }
        }

        return $backendValue ?: $anyValue;
    }
}
