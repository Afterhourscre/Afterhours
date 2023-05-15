<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\RewardPoints\Model\EarnRule;

use Aheadworks\RewardPoints\Api\Data\EarnRuleInterface;
use Aheadworks\RewardPoints\Api\EarnRuleRepositoryInterface;
use Aheadworks\RewardPoints\Model\Config;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class CategoryPromoTextResolver
 * @package Aheadworks\RewardPoints\Model\EarnRule
 */
class CategoryPromoTextResolver
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var EarnRuleRepositoryInterface
     */
    private $earnRuleRepository;

    /**
     * @param Config $config
     * @param EarnRuleRepositoryInterface $earnRuleRepository
     */
    public function __construct(
        Config $config,
        EarnRuleRepositoryInterface $earnRuleRepository
    ) {
        $this->config = $config;
        $this->earnRuleRepository = $earnRuleRepository;
    }

    /**
     * Get category product promo text
     *
     * @param int[] $appliedRuleIds
     * @param int|null $storeId
     * @return string
     */
    public function getPromoText($appliedRuleIds, $storeId = null)
    {
        $promoText = '';
        $appliedRulesCount = count($appliedRuleIds);
        switch (true) {
            case $appliedRulesCount == 1:
                $ruleId = reset($appliedRuleIds);
                try {
                    /** @var EarnRuleInterface $rule */
                    $rule = $this->earnRuleRepository->get($ruleId, $storeId);
                    $promoText = $rule->getCurrentLabels()->getCategoryPromoText();
                } catch (NoSuchEntityException $e) {
                }
                break;

            case $appliedRulesCount > 1:
                $promoText = $this->config->getCategoryProductPromoText($storeId)
                    ? $this->config->getCategoryProductPromoText($storeId)
                    : '';
                break;
        }

        return $promoText;
    }
}
