<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Acr\Model\Source\Rule;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\SalesRule\Api\Data\RuleInterface as SalesruleInterface;
use Magento\SalesRule\Model\Data\Rule as Salesrule;
use Magento\SalesRule\Api\RuleRepositoryInterface as SalesruleRepository;
use Magento\Framework\Api\SearchCriteriaBuilder;

/**
 * Class CouponRule
 * @package Aheadworks\Acr\Model\Source\Rule
 */
class CouponRule implements OptionSourceInterface
{
    /**
     * @var SalesruleRepository
     */
    private $salesruleRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @param SalesruleRepository $salesruleRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        SalesruleRepository $salesruleRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->salesruleRepository = $salesruleRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        $result = [];

        $this->searchCriteriaBuilder
            ->addFilter(Salesrule::KEY_USE_AUTO_GENERATION, true)
            ->addFilter(Salesrule::KEY_IS_ACTIVE, true);

        /** @var SalesruleInterface[] $rules */
        $rules = $this->salesruleRepository->getList(
            $this->searchCriteriaBuilder->create()
        )->getItems();

        /** @var SalesruleInterface $rule */
        foreach ($rules as $rule) {
            $result[] = [
                'value' => $rule->getRuleId(),
                'label' => $rule->getName()
            ];
        }

        return $result;
    }
}
