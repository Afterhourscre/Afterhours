<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Rule interface
 * @api
 */
interface RuleInterface extends ExtensibleDataInterface
{
    /**#@+
     * Constants defined for keys of the data array.
     * Identical to the name of the getter in snake case
     */
    const RULE_ID = 'rule_id';
    const IS_ACTIVE = 'is_active';
    const NAME = 'name';
    const FROM_DATE = 'from_date';
    const TO_DATE = 'to_date';
    const PRIORITY = 'priority';
    const LABEL_ID = 'label_id';
    const WEBSITE_IDS = 'website_ids';
    const CUSTOMER_GROUPS = 'customer_groups';
    const PRODUCT_CONDITION = 'product_condition';
    const FRONTEND_LABEL_TEXT_STORE_VALUES = 'frontend_label_text_store_values';
    const FRONTEND_LABEL_TEXT = 'frontend_label_text';
    /**#@-*/

    /**
     * Get Rule ID
     *
     * @return int
     */
    public function getRuleId();

    /**
     * Set Rule ID
     *
     * @param int $ruleId
     * @return $this
     */
    public function setRuleId($ruleId);

    /**
     * Get is active
     *
     * @return bool
     */
    public function getIsActive();

    /**
     * Set is active
     *
     * @param bool $isActive
     * @return $this
     */
    public function setIsActive($isActive);

    /**
     * Get name
     *
     * @return string
     */
    public function getName();

    /**
     * Set name
     *
     * @param string $name
     * @return $this
     */
    public function setName($name);

    /**
     * Get from date
     *
     * @return string|null
     */
    public function getFromDate();

    /**
     * Set from date
     *
     * @param string|null $fromDate
     * @return $this
     */
    public function setFromDate($fromDate);

    /**
     * Get to date
     *
     * @return string|null
     */
    public function getToDate();

    /**
     * Set to date
     *
     * @param string|null $toDate
     * @return $this
     */
    public function setToDate($toDate);

    /**
     * Get priority
     *
     * @return int
     */
    public function getPriority();

    /**
     * Set priority
     *
     * @param int $priority
     * @return $this
     */
    public function setPriority($priority);

    /**
     * Get label ID
     *
     * @return int|null
     */
    public function getLabelId();

    /**
     * Set label ID
     *
     * @param int|null $labelId
     * @return $this
     */
    public function setLabelId($labelId);

    /**
     * Get website IDs
     *
     * @return int[]
     */
    public function getWebsiteIds();

    /**
     * Set website IDs
     *
     * @param int[] $websiteIds
     * @return $this
     */
    public function setWebsiteIds($websiteIds);

    /**
     * Get allowed customer groups for rule
     *
     * @return int[]
     */
    public function getCustomerGroups();

    /**
     * Set allowed customer groups for rule
     *
     * @param int[] $customerGroups
     * @return $this
     */
    public function setCustomerGroups($customerGroups);

    /**
     * Get product condition
     *
     * @return \Aheadworks\OnSale\Api\Data\ConditionInterface
     */
    public function getProductCondition();

    /**
     * Set product condition
     *
     * @param \Aheadworks\OnSale\Api\Data\ConditionInterface $productCondition
     * @return $this
     */
    public function setProductCondition($productCondition);

    /**
     * Get store frontend label text values
     *
     * @return \Aheadworks\OnSale\Api\Data\LabelTextStoreValueInterface[]
     */
    public function getFrontendLabelTextStoreValues();
    /**
     * Set store frontend label text values
     *
     * @param \Aheadworks\OnSale\Api\Data\LabelTextStoreValueInterface[] $frontendLabelTextValues
     * @return $this
     */
    public function setFrontendLabelTextStoreValues($frontendLabelTextValues);

    /**
     * Get frontend label text
     *
     * @return \Aheadworks\OnSale\Api\Data\LabelTextInterface
     */
    public function getFrontendLabelText();

    /**
     * Set frontend label text
     *
     * @param \Aheadworks\OnSale\Api\Data\LabelTextInterface $frontendLabelText
     * @return $this
     */
    public function setFrontendLabelText($frontendLabelText);

    /**
     * Retrieve existing extension attributes object or create a new one
     *
     * @return \Aheadworks\OnSale\Api\Data\RuleExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object
     *
     * @param \Aheadworks\OnSale\Api\Data\RuleExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Aheadworks\OnSale\Api\Data\RuleExtensionInterface $extensionAttributes
    );
}
