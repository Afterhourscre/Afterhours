<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Acr\Model\Source;

use Magento\Customer\Api\Data\GroupInterface;
use Magento\Customer\Api\GroupRepositoryInterface;
use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Convert\DataObject as ConvertDataObject;

/**
 * Class Groups
 * @package Aheadworks\Acr\Model\Source
 */
class Groups implements OptionSourceInterface
{
    /**
     * @var GroupRepositoryInterface
     */
    private $groupRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var ConvertDataObject
     */
    private $objectConverter;

    /**
     * @var array
     */
    private $options;

    /**
     * @param GroupRepositoryInterface $groupRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param ConvertDataObject $objectConverter
     */
    public function __construct(
        GroupRepositoryInterface $groupRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        ConvertDataObject $objectConverter
    ) {
        $this->groupRepository = $groupRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->objectConverter = $objectConverter;
    }

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        if (!$this->options) {
            $this->searchCriteriaBuilder->addFilter(
                'customer_group_id',
                GroupInterface::NOT_LOGGED_IN_ID,
                'neq'
            );
            $groupsOptions = $this->objectConverter->toOptionArray(
                $this->groupRepository->getList($this->searchCriteriaBuilder->create())->getItems(),
                'id',
                'code'
            );
            array_unshift($groupsOptions, [
                'value' => 'all',
                'label' => __('All groups')
            ]);
            $this->options = $groupsOptions;
        }

        return $this->options;
    }
}
