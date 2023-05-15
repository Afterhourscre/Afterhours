<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Model\Source\Customer;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Customer\Model\ResourceModel\Group\Collection;

/**
 * Class Group
 * @package Aheadworks\OnSale\Model\Source\Customer
 */
class Group implements OptionSourceInterface
{
    /**
     * Constant for 'All Groups' option
     */
    const ALL_GROUPS = 'all';

    /**
     * @var Collection
     */
    private $customerGroupCollection;

    /**
     * @param Collection $customerGroupCollection
     */
    public function __construct(
        Collection $customerGroupCollection
    ) {
        $this->customerGroupCollection = $customerGroupCollection;
    }

    /**
     * Get all customer group ids
     *
     * @return array
     */
    public function getAllCustomerGroupIds()
    {
        return $this->customerGroupCollection->getAllIds();
    }

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        $customerGroups = $this->customerGroupCollection->toOptionArray();
        array_unshift($customerGroups, $this->getAllGroupsOption());
        return $customerGroups;
    }

    /**
     * Prepare 'All Groups' option
     *
     * @return array
     */
    private function getAllGroupsOption()
    {
        return [
            'value' => self::ALL_GROUPS,
            'label' =>__('All Groups')
        ];
    }
}
