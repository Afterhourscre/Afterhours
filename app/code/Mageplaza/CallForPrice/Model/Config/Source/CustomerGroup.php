<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_CallForPrice
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\CallForPrice\Model\Config\Source;

use Magento\Customer\Api\GroupRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Convert\DataObject;
use Magento\Framework\Option\ArrayInterface;

/**
 * Class CustomerGroup
 * @package Mageplaza\CallForPrice\Model\Config\Source
 */
class CustomerGroup implements ArrayInterface
{
    /**
     * @var \Magento\Customer\Api\GroupRepositoryInterface
     */
    protected $_groupRepository;

    /**
     * @var \Magento\Framework\Convert\DataObject
     */
    protected $_objectConverter;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    protected $_searchCriteriaBuilder;

    /**
     * CustomerGroup constructor.
     *
     * @param GroupRepositoryInterface $groupRepository
     * @param DataObject $dataObject
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        GroupRepositoryInterface $groupRepository,
        DataObject $dataObject,
        SearchCriteriaBuilder $searchCriteriaBuilder
    )
    {
        $this->_groupRepository       = $groupRepository;
        $this->_objectConverter       = $dataObject;
        $this->_searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function toOptionArray()
    {
        $customerGroups = $this->_groupRepository->getList($this->_searchCriteriaBuilder->create())->getItems();

        $options = $this->_objectConverter->toOptionArray($customerGroups, 'id', 'code');

        array_unshift($options, ['value' => '', 'label' => __('-- Please Select --')]);

        return $options;
    }
}
