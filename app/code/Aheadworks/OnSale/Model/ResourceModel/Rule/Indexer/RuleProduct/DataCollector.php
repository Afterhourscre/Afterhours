<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Model\ResourceModel\Rule\Indexer\RuleProduct;

use Aheadworks\OnSale\Api\Data\RuleInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Aheadworks\OnSale\Model\Rule\LabelText\StoreResolver as LabelTextStoreResolver;
use Magento\Store\Model\Store;

/**
 * Class DataCollector
 *
 * @package Aheadworks\OnSale\Model\ResourceModel\Rule\Indexer\RuleProduct
 */
class DataCollector
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var LabelTextStoreResolver
     */
    private $labelStoreTextResolver;

    /**
     * @param StoreManagerInterface $storeManager
     * @param LabelTextStoreResolver $labelStoreTextResolver
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        LabelTextStoreResolver $labelStoreTextResolver
    ) {
        $this->storeManager = $storeManager;
        $this->labelStoreTextResolver = $labelStoreTextResolver;
    }

    /**
     * Prepare rule data for all matching products
     *
     * @param RuleInterface $rule
     * @return array
     */
    public function prepareRuleData(RuleInterface $rule)
    {
        $data = [];
        if ($this->isRuleAllowed($rule)) {
            $websiteIds = $rule->getWebsiteIds();
            $productIds = $rule->getProductRule()->getMatchingProductIds($websiteIds);
            $customerGroupIds = $rule->getCustomerGroups();
            $ruleId = $rule->getRuleId();
            $fromDate = $rule->getFromDate();
            $toDate = $rule->getToDate();
            $priority = $rule->getPriority();
            $labelId = $rule->getLabelId();
            $storeValueItems = $rule->getFrontendLabelTextStoreValues();

            foreach ($productIds as $productId => $validationByWebsite) {
                foreach ($websiteIds as $websiteId) {
                    if ($stores = $this->getWebsiteStores($websiteId)) {
                        if (empty($validationByWebsite[$websiteId])) {
                            continue;
                        }
                        /** @var Store $store */
                        foreach ($stores as $store) {
                            $storeId = $store->getStoreId();
                            $labelText = $this->labelStoreTextResolver->getLabelTextAsObject(
                                $storeValueItems,
                                $storeId
                            );
                            $largeText = $labelText ? $labelText->getValueLarge() :  '';
                            $mediumText = $labelText ? $labelText->getValueMedium() :  '';
                            $smallText = $labelText ? $labelText->getValueSmall() :  '';

                            foreach ($customerGroupIds as $customerGroupId) {
                                $data[] = [
                                    RuleProductInterface::RULE_ID => $ruleId,
                                    RuleProductInterface::FROM_DATE => $fromDate,
                                    RuleProductInterface::TO_DATE => $toDate,
                                    RuleProductInterface::CUSTOMER_GROUP_ID => $customerGroupId,
                                    RuleProductInterface::PRODUCT_ID => $productId,
                                    RuleProductInterface::PRIORITY => $priority,
                                    RuleProductInterface::STORE_ID => $storeId,
                                    RuleProductInterface::LABEL_ID => $labelId,
                                    RuleProductInterface::LABEL_TEXT_LARGE => $largeText,
                                    RuleProductInterface::LABEL_TEXT_MEDIUM => $mediumText,
                                    RuleProductInterface::LABEL_TEXT_SMALL => $smallText,
                                ];
                            }
                        }
                    }
                }
            }
        }

        return $data;
    }

    /**
     * Prepare rule product data for specific product
     *
     * @param RuleInterface $rule
     * @param ProductInterface $product
     * @return array
     */
    public function prepareRuleProductData(RuleInterface $rule, ProductInterface $product)
    {
        $data = [];
        if ($this->isRuleAllowed($rule)) {
            $websiteIds = array_intersect($product->getWebsiteIds(), $rule->getWebsiteIds());

            $productConditions = $rule->getProductRule()->getConditions();
            if (!$productConditions->validate($product)) {
                return $data;
            }

            $productId = $product->getId();
            $customerGroupIds = $rule->getCustomerGroups();
            $ruleId = $rule->getRuleId();
            $fromDate = $rule->getFromDate();
            $toDate = $rule->getToDate();
            $priority = $rule->getPriority();
            $labelId = $rule->getLabelId();
            $storeValueItems = $rule->getFrontendLabelTextStoreValues();

            foreach ($websiteIds as $websiteId) {
                if ($stores = $this->getWebsiteStores($websiteId)) {
                    /** @var Store $store */
                    foreach ($stores as $store) {
                        $storeId = $store->getStoreId();
                        $labelText = $this->labelStoreTextResolver->getLabelTextAsObject(
                            $storeValueItems,
                            $storeId
                        );
                        $largeText = $labelText ? $labelText->getValueLarge() :  '';
                        $mediumText = $labelText ? $labelText->getValueMedium() :  '';
                        $smallText = $labelText ? $labelText->getValueSmall() :  '';

                        foreach ($customerGroupIds as $customerGroupId) {
                            $data[] = [
                                RuleProductInterface::RULE_ID => $ruleId,
                                RuleProductInterface::FROM_DATE => $fromDate,
                                RuleProductInterface::TO_DATE => $toDate,
                                RuleProductInterface::CUSTOMER_GROUP_ID => $customerGroupId,
                                RuleProductInterface::PRODUCT_ID => $productId,
                                RuleProductInterface::PRIORITY => $priority,
                                RuleProductInterface::STORE_ID => $storeId,
                                RuleProductInterface::LABEL_ID => $labelId,
                                RuleProductInterface::LABEL_TEXT_LARGE => $largeText,
                                RuleProductInterface::LABEL_TEXT_MEDIUM => $mediumText,
                                RuleProductInterface::LABEL_TEXT_SMALL => $smallText,
                            ];
                        }
                    }
                }
            }
        }

        return $data;
    }

    /**
     * Get stores associated with website
     *
     * @param $websiteId
     * @return mixed
     */
    private function getWebsiteStores($websiteId)
    {
        try {
            return $this->storeManager->getWebsite($websiteId)->getStores();
        } catch (\Exception $exception) {
            return false;
        }
    }

    /**
     * Check if rule is allowed to be processed
     *
     * @param RuleInterface $rule
     * @return bool
     */
    private function isRuleAllowed($rule)
    {
        return $rule->getIsActive()
            && $rule->getProductCondition()
            && $rule->getLabelId();
    }
}
