<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Model;

use Aheadworks\OnSale\Api\Data\RuleInterface;
use Magento\Framework\Model\AbstractModel;
use Aheadworks\OnSale\Model\ResourceModel\Rule as ResourceRule;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Data\Collection\AbstractDb;
use Aheadworks\OnSale\Model\Rule\ObjectDataProcessor as RuleDataProcessor;
use Aheadworks\OnSale\Model\Rule\Validator\General as GeneralValidator;
use Aheadworks\OnSale\Model\Rule\ProductFactory as ProductRuleFactory;
use Aheadworks\OnSale\Model\Rule\Product as ProductRule;
use Aheadworks\OnSale\Model\Converter\Condition as ConditionConverter;

/**
 * Class Rule
 *
 * @package Aheadworks\OnSale\Model
 */
class Rule extends AbstractModel implements RuleInterface
{
    /**
     * @var ProductRule
     */
    private $productRule;

    /**
     * @var ProductRuleFactory
     */
    private $productRuleFactory;

    /**
     * @var GeneralValidator
     */
    private $validator;

    /**
     * @var RuleDataProcessor
     */
    private $ruleDataProcessor;

    /**
     * @var ConditionConverter
     */
    private $conditionConverter;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param RuleDataProcessor $ruleDataProcessor
     * @param GeneralValidator $validator
     * @param ConditionConverter $conditionConverter
     * @param ProductRuleFactory $productRuleFactory
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        RuleDataProcessor $ruleDataProcessor,
        GeneralValidator $validator,
        ConditionConverter $conditionConverter,
        ProductRuleFactory $productRuleFactory,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            $resource,
            $resourceCollection,
            $data
        );
        $this->conditionConverter = $conditionConverter;
        $this->productRuleFactory = $productRuleFactory;
        $this->validator = $validator;
        $this->ruleDataProcessor = $ruleDataProcessor;
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(ResourceRule::class);
    }

    /**
     * {@inheritdoc}
     */
    public function getRuleId()
    {
        return $this->getData(self::RULE_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setRuleId($ruleId)
    {
        return $this->setData(self::RULE_ID, $ruleId);
    }

    /**
     * {@inheritdoc}
     */
    public function getIsActive()
    {
        return $this->getData(self::IS_ACTIVE);
    }

    /**
     * {@inheritdoc}
     */
    public function setIsActive($isActive)
    {
        return $this->setData(self::IS_ACTIVE, $isActive);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->getData(self::NAME);
    }

    /**
     * {@inheritdoc}
     */
    public function setName($name)
    {
        return $this->setData(self::NAME, $name);
    }

    /**
     * {@inheritdoc}
     */
    public function getFromDate()
    {
        return $this->getData(self::FROM_DATE);
    }

    /**
     * {@inheritdoc}
     */
    public function setFromDate($fromDate)
    {
        return $this->setData(self::FROM_DATE, $fromDate);
    }

    /**
     * {@inheritdoc}
     */
    public function getToDate()
    {
        return $this->getData(self::TO_DATE);
    }

    /**
     * {@inheritdoc}
     */
    public function setToDate($toDate)
    {
        return $this->setData(self::TO_DATE, $toDate);
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        return $this->getData(self::PRIORITY);
    }

    /**
     * {@inheritdoc}
     */
    public function setPriority($priority)
    {
        return $this->setData(self::PRIORITY, $priority);
    }

    /**
     * {@inheritdoc}
     */
    public function getLabelId()
    {
        return $this->getData(self::LABEL_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setLabelId($labelId)
    {
        return $this->setData(self::LABEL_ID, $labelId);
    }

    /**
     * {@inheritdoc}
     */
    public function getWebsiteIds()
    {
        return $this->getData(self::WEBSITE_IDS);
    }

    /**
     * {@inheritdoc}
     */
    public function setWebsiteIds($websiteIds)
    {
        return $this->setData(self::WEBSITE_IDS, $websiteIds);
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomerGroups()
    {
        return $this->getData(self::CUSTOMER_GROUPS);
    }

    /**
     * {@inheritdoc}
     */
    public function setCustomerGroups($customerGroups)
    {
        return $this->setData(self::CUSTOMER_GROUPS, $customerGroups);
    }

    /**
     * {@inheritdoc}
     */
    public function getProductCondition()
    {
        return $this->getData(self::PRODUCT_CONDITION);
    }

    /**
     * {@inheritdoc}
     */
    public function setProductCondition($productCondition)
    {
        return $this->setData(self::PRODUCT_CONDITION, $productCondition);
    }

    /**
     * {@inheritdoc}
     */
    public function getFrontendLabelTextStoreValues()
    {
        return $this->getData(self::FRONTEND_LABEL_TEXT_STORE_VALUES);
    }

    /**
     * {@inheritdoc}
     */
    public function setFrontendLabelTextStoreValues($frontendLabelTextValues)
    {
        return $this->setData(self::FRONTEND_LABEL_TEXT_STORE_VALUES, $frontendLabelTextValues);
    }

    /**
     * {@inheritdoc}
     */
    public function getFrontendLabelText()
    {
        return $this->getData(self::FRONTEND_LABEL_TEXT);
    }

    /**
     * {@inheritdoc}
     */
    public function setFrontendLabelText($frontendLabelText)
    {
        return $this->setData(self::FRONTEND_LABEL_TEXT, $frontendLabelText);
    }

    /**
     * {@inheritdoc}
     */
    public function getExtensionAttributes()
    {
        return $this->getData(self::EXTENSION_ATTRIBUTES_KEY);
    }

    /**
     * {@inheritdoc}
     */
    public function setExtensionAttributes(
        \Aheadworks\OnSale\Api\Data\RuleExtensionInterface $extensionAttributes
    ) {
        return $this->setData(self::EXTENSION_ATTRIBUTES_KEY, $extensionAttributes);
    }

    /**
     * {@inheritdoc}
     */
    public function beforeSave()
    {
        $this->ruleDataProcessor->prepareDataBeforeSave($this);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function afterLoad()
    {
        $this->ruleDataProcessor->prepareDataAfterLoad($this);
        return $this;
    }

    /**
     * Get product rule model with loaded conditions
     *
     * @return ProductRule
     */
    public function getProductRule()
    {
        if (!$this->productRule) {
            $this->productRule = $this->productRuleFactory->create();
            if ($productConditionDataModel = $this->getProductCondition()) {
                $productConditionArray = $this->conditionConverter->dataModelToArray($productConditionDataModel);
                $this->productRule->setConditions([])
                    ->getConditions()
                    ->loadArray($productConditionArray);
            } else {
                $this->productRule->setConditions([])
                    ->getConditions()
                    ->asArray();
            }
        }

        return $this->productRule;
    }

    /**
     * {@inheritdoc}
     */
    protected function _getValidationRulesBeforeSave()
    {
        return $this->validator;
    }
}
