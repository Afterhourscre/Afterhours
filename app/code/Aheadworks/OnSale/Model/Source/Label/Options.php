<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Model\Source\Label;

use Magento\Framework\Data\OptionSourceInterface;
use Aheadworks\OnSale\Api\LabelRepositoryInterface;
use Magento\Framework\Convert\DataObject;
use Aheadworks\OnSale\Api\Data\LabelInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;

/**
 * Class Options
 *
 * @package Aheadworks\OnSale\Model\Source\Label
 */
class Options implements OptionSourceInterface
{
    /**
     * @var LabelRepositoryInterface
     */
    private $labelRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var DataObject
     */
    private $objectConverter;

    /**
     * @param LabelRepositoryInterface $labelRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param DataObject $objectConverter
     */
    public function __construct(
        LabelRepositoryInterface $labelRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        DataObject $objectConverter
    ) {
        $this->labelRepository = $labelRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->objectConverter = $objectConverter;
    }

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        $labels = $this->labelRepository->getList($this->searchCriteriaBuilder->create())->getItems();
        $options = $this->objectConverter->toOptionArray($labels, LabelInterface::LABEL_ID, LabelInterface::NAME);

        return $options;
    }
}
