<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Model\Label\Block\Rule;

use Aheadworks\OnSale\Api\Data\LabelInterface;
use Aheadworks\OnSale\Api\LabelRepositoryInterface;
use Aheadworks\OnSale\Model\ResourceModel\Rule\Indexer\RuleProduct\RuleProductInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Stdlib\DateTime as StdlibDateTime;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Aheadworks\OnSale\Model\ResourceModel\Rule as RuleResource;

/**
 * Class LabelProvider
 *
 * @package Aheadworks\OnSale\Model\Label\Block
 */
class Loader
{
    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var LabelRepositoryInterface
     */
    private $labelRepository;

    /**
     * @var RuleResource
     */
    private $ruleResource;

    /**
     * @var DateTime
     */
    private $dateTime;

    /**
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param LabelRepositoryInterface $labelRepository
     * @param RuleResource $ruleResource
     * @param DateTime $dateTime
     */
    public function __construct(
        SearchCriteriaBuilder $searchCriteriaBuilder,
        LabelRepositoryInterface $labelRepository,
        RuleResource $ruleResource,
        DateTime $dateTime
    ) {
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->labelRepository = $labelRepository;
        $this->ruleResource = $ruleResource;
        $this->dateTime = $dateTime;
    }

    /**
     * Retrieve available rules for product
     *
     * @param Product|ProductInterface $product
     * @param int $customerGroupId
     * @return array
     */
    public function getAvailableRulesForProduct($product, $customerGroupId)
    {
        $productId = $product->getId();
        $storeId = $product->getStoreId();
        $currentDate = $this->dateTime->gmtDate(StdlibDateTime::DATE_PHP_FORMAT);

        $availableRules = $this->ruleResource->getSortedRulesDataForProduct(
            $productId,
            $customerGroupId,
            $storeId,
            $currentDate
        );

        return $availableRules;
    }

    /**
     * Load labels by label ids
     *
     * @param array $availableRules
     * @return LabelInterface[]
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getLabelsForRules($availableRules)
    {
        $labelIds = $this->getLabelIds($availableRules);
        $this->searchCriteriaBuilder->addFilter(LabelInterface::LABEL_ID, $labelIds, 'in');
        $labels = $this->labelRepository->getList($this->searchCriteriaBuilder->create())->getItems();

        return $labels;
    }

    /**
     * Retrieve label ids from available rules
     *
     * @param array $availableRules
     * @return array
     */
    private function getLabelIds($availableRules)
    {
        $labelIds = [];
        foreach ($availableRules as $availableRule) {
            $labelIds[] = $availableRule[RuleProductInterface::LABEL_ID];
        }

        return $labelIds;
    }
}
