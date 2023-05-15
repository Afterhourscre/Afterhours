<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Coupongenerator\Model\Converter;

use Aheadworks\Coupongenerator\Api\Data\SalesruleInterfaceFactory;
use Magento\SalesRule\Api\Data\RuleInterface;
use Magento\SalesRule\Api\Data\RuleExtensionFactory;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\Api\DataObjectHelper;

/**
 * Class Salesrule
 * @package Aheadworks\Coupongenerator\Model\Converter
 */
class Salesrule
{
    /**
     * @var \Magento\Framework\Reflection\DataObjectProcessor $dataObjectProcessor
     */
    private $dataObjectProcessor;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var \Magento\SalesRule\Api\Data\RuleExtensionFactory
     */
    private $magentoRuleExtensionFactory;

    /**
     * @var \Aheadworks\Coupongenerator\Api\Data\SalesruleInterfaceFactory
     */
    private $salesruleFactory;

    /**
     * @param DataObjectProcessor $dataObjectProcessor
     * @param DataObjectHelper $dataObjectHelper
     * @param RuleExtensionFactory $magentoRuleExtensionFactory
     * @param SalesruleInterfaceFactory $salesruleFactory
     */
    public function __construct(
        DataObjectProcessor $dataObjectProcessor,
        DataObjectHelper $dataObjectHelper,
        RuleExtensionFactory $magentoRuleExtensionFactory,
        SalesruleInterfaceFactory $salesruleFactory
    ) {
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->magentoRuleExtensionFactory = $magentoRuleExtensionFactory;
        $this->salesruleFactory = $salesruleFactory;
    }

    /**
     * Convert rule with extended attribute to data array
     *
     * @param \Magento\SalesRule\Api\Data\RuleInterface $rule
     * @return array
     */
    public function toFormData(RuleInterface $rule)
    {
        $ruleData = $this->dataObjectProcessor->buildOutputDataArray(
            $rule,
            RuleInterface::class
        );

        $ruleData = $this->getDataArrayWithPreparedMultiselectValues($ruleData);
        $ruleData = $this->getDataArrayWithPreparedCheckboxValues($ruleData);

        $extensionAttributes = $rule->getExtensionAttributes();
        if ($extensionAttributes && $extensionAttributes->getAwCoupongeneratorData()) {
            /** @var \Aheadworks\Coupongenerator\Api\Data\SalesruleInterface $salesruleDataObject */
            $salesruleDataObject = $extensionAttributes->getAwCoupongeneratorData();

            $salesruleData = [
                'aw_coupongenerator_expiration' => $salesruleDataObject->getExpirationDays(),
                'aw_coupongenerator_length'     => $salesruleDataObject->getCouponLength(),
                'aw_coupongenerator_format'     => $salesruleDataObject->getCodeFormat(),
                'aw_coupongenerator_prefix'     => $salesruleDataObject->getCodePrefix(),
                'aw_coupongenerator_suffix'     => $salesruleDataObject->getCodeSuffix(),
                'aw_coupongenerator_dash'       => $salesruleDataObject->getCodeDash(),
            ];

            $ruleData = array_merge($ruleData, $salesruleData);
        }
        return $ruleData;
    }

    /**
     * Prepare values for multiselect fields
     *
     * @param array $ruleData
     * @return array
     */
    private function getDataArrayWithPreparedMultiselectValues($ruleData)
    {
        $multiselectFields = $this->getMultiselectFieldNames();
        foreach ($multiselectFields as $multiselectFieldName) {
            if (isset($ruleData[$multiselectFieldName])) {
                foreach ($ruleData[$multiselectFieldName] as $key => $value) {
                    $ruleData[$multiselectFieldName][$key] = (string)$value;
                }
            }
        }
        return $ruleData;
    }

    /**
     * Return array of multiselect field names
     *
     * @return array
     */
    private function getMultiselectFieldNames()
    {
        return ['website_ids', 'customer_group_ids'];
    }

    /**
     * Prepare values for checkbox fields
     *
     * @param array $ruleData
     * @return array
     */
    private function getDataArrayWithPreparedCheckboxValues($ruleData)
    {
        $checkboxFields = $this->getCheckboxFieldNames();
        foreach ($checkboxFields as $checkboxFieldName) {
            if (isset($ruleData[$checkboxFieldName])) {
                $ruleData[$checkboxFieldName] = (string)$ruleData[$checkboxFieldName];
            }
        }
        return $ruleData;
    }

    /**
     * Return array of checkbox field names
     *
     * @return array
     */
    private function getCheckboxFieldNames()
    {
        return ['is_active', 'apply_to_shipping', 'stop_rules_processing'];
    }

    /**
     * Populate rule with extended attribute with form data array
     *
     * @param \Magento\SalesRule\Api\Data\RuleInterface $rule
     * @param array $formData
     * @return \Magento\SalesRule\Api\Data\RuleInterface $rule
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function populateWithFormData(RuleInterface $rule, array $formData)
    {
        $this->dataObjectHelper->populateWithArray(
            $rule,
            $formData,
            RuleInterface::class
        );

        /** @var \Magento\SalesRule\Api\Data\RuleExtension $magentoRuleExtension */
        $extensionAttributes = $rule->getExtensionAttributes();
        if ($extensionAttributes === null) {
            $extensionAttributes = $this->magentoRuleExtensionFactory->create();
        }

        /** @var \Aheadworks\Coupongenerator\Api\Data\SalesruleInterface $salesruleDataObject */
        $salesruleDataObject = $extensionAttributes->getAwCoupongeneratorData();
        if ($salesruleDataObject === null) {
            $salesruleDataObject = $this->salesruleFactory->create();
        }

        if (isset($formData['aw_coupongenerator_expiration'])) {
            $salesruleDataObject->setExpirationDays($formData['aw_coupongenerator_expiration']);
        }
        if (isset($formData['aw_coupongenerator_length'])) {
            $salesruleDataObject->setCouponLength($formData['aw_coupongenerator_length']);
        }
        if (isset($formData['aw_coupongenerator_format'])) {
            $salesruleDataObject->setCodeFormat($formData['aw_coupongenerator_format']);
        }
        if (isset($formData['aw_coupongenerator_prefix'])) {
            $salesruleDataObject->setCodePrefix($formData['aw_coupongenerator_prefix']);
        }
        if (isset($formData['aw_coupongenerator_suffix'])) {
            $salesruleDataObject->setCodeSuffix($formData['aw_coupongenerator_suffix']);
        }
        if (isset($formData['aw_coupongenerator_dash'])) {
            $salesruleDataObject->setCodeDash($formData['aw_coupongenerator_dash']);
        }

        $extensionAttributes->setAwCoupongeneratorData($salesruleDataObject);
        $rule->setExtensionAttributes($extensionAttributes);

        return $rule;
    }
}
