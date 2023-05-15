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
 * Class ProductPromoTextResolver
 * @package Aheadworks\RewardPoints\Model\EarnRule
 */
class ProductPromoTextResolver
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
     * Get product promo text
     *
     * @param int[] $appliedRuleIds
     * @param int|null $storeId
     * @param bool $loggedIn
     * @return string
     */
    public function getPromoText($appliedRuleIds, $storeId = null, $loggedIn = true)
    {
        $promoText = '';
        $appliedRulesCount = count($appliedRuleIds);
        if ($appliedRulesCount == 1) {
            $ruleId = reset($appliedRuleIds);
            try {
                /** @var EarnRuleInterface $rule */
                $rule = $this->earnRuleRepository->get($ruleId, $storeId);
                $promoText = $rule->getCurrentLabels()->getProductPromoText();
            } catch (NoSuchEntityException $e) {
            }
        }

        if (empty($promoText)) {
            $promoText = $loggedIn
                ? $this->config->getProductPromoTextForRegisteredCustomers($storeId)
                : $this->config->getProductPromoTextForNotLoggedInVisitors($storeId);
        }

        return $promoText;
    }
}
