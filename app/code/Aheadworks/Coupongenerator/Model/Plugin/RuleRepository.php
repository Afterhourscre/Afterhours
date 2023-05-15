<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Coupongenerator\Model\Plugin;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\SalesRule\Api\Data\RuleInterface;
use Magento\SalesRule\Api\RuleRepositoryInterface;
use Magento\SalesRule\Api\Data\RuleExtensionFactory;
use Aheadworks\Coupongenerator\Api\Data\SalesruleInterface;
use Aheadworks\Coupongenerator\Api\Data\SalesruleInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;
use Aheadworks\Coupongenerator\Model\SalesruleRepository;

/**
 * Class RuleRepository
 * @package Aheadworks\Coupongenerator\Model\Plugin
 * @codeCoverageIgnore
 */
class RuleRepository
{
    /**
     * @var \Aheadworks\Coupongenerator\Api\Data\SalesruleInterfaceFactory
     */
    private $salesruleFactory;

    /**
     * @var \Aheadworks\Coupongenerator\Model\SalesruleRepository
     */
    private $salesruleRepository;

    /**
     * @var \Magento\SalesRule\Api\Data\RuleExtensionFactory
     */
    private $magentoRuleExtensionFactory;

    /**
     * @var \Magento\Framework\Api\DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @param SalesruleInterfaceFactory $salesruleFactory
     * @param RuleExtensionFactory $magentoRuleExtensionFactory
     * @param SalesruleRepository $salesruleRepository
     * @param DataObjectHelper $dataObjectHelper
     */
    public function __construct(
        SalesruleInterfaceFactory $salesruleFactory,
        SalesruleRepository $salesruleRepository,
        RuleExtensionFactory $magentoRuleExtensionFactory,
        DataObjectHelper $dataObjectHelper
    ) {
        $this->salesruleFactory = $salesruleFactory;
        $this->salesruleRepository = $salesruleRepository;
        $this->magentoRuleExtensionFactory = $magentoRuleExtensionFactory;
        $this->dataObjectHelper = $dataObjectHelper;
    }

    /**
     * Extend Magento's salesrule by coupongenerator data
     *
     * @param RuleRepositoryInterface $subject
     * @param RuleInterface $resultRule
     * @return RuleInterface
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetById(RuleRepositoryInterface $subject, RuleInterface $resultRule)
    {
        $resultRule = $this->addCoupongeneratorSalesruleInfo($resultRule);

        return $resultRule;
    }

    /**
     * Save coupongenerator data after Magento's salesrule save
     *
     * @param RuleRepositoryInterface $subject
     * @param RuleInterface $resultRule
     * @return RuleInterface
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterSave(RuleRepositoryInterface $subject, RuleInterface $resultRule)
    {
        $resultRule = $this->saveCoupongeneratorSalesruleInfo($resultRule);

        return $resultRule;
    }

    /**
     * Save coupongenerator data after Magento's salesrule save
     *
     * @param RuleRepositoryInterface $subject
     * @param \Closure $proceed
     * @param int $id
     * @return bool true on success
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundDeleteById(RuleRepositoryInterface $subject, \Closure $proceed, $id)
    {
        $result = $proceed($id);

        if ($result) {
            $this->salesruleRepository->deleteByRuleId($id);
        }

        return $result;
    }

    /**
     * Add coupon generator data
     *
     * @param RuleInterface $rule
     * @return RuleInterface
     */
    private function addCoupongeneratorSalesruleInfo(RuleInterface $rule)
    {
        $extensionAttributes = $rule->getExtensionAttributes();
        if ($extensionAttributes && $extensionAttributes->getAwCoupongeneratorData()) {
            return $rule;
        }

        /** @var \Aheadworks\Coupongenerator\Api\Data\SalesruleInterface $salesrule */
        try {
            $salesrule = $this->salesruleRepository->getByRuleId($rule->getRuleId());

            /** @var \Magento\SalesRule\Api\Data\RuleExtension $magentoRuleExtension */
            $magentoRuleExtension = $extensionAttributes ?
                $extensionAttributes :
                $this->magentoRuleExtensionFactory->create();
            $magentoRuleExtension->setAwCoupongeneratorData($salesrule);
            $rule->setExtensionAttributes($magentoRuleExtension);
        } catch (NoSuchEntityException $e) {
        }

        return $rule;
    }

    /**
     * Save coupongenerator data
     *
     * @param RuleInterface $rule
     * @return RuleInterface $rule
     */
    private function saveCoupongeneratorSalesruleInfo(RuleInterface $rule)
    {
        $extensionAttributes = $rule->getExtensionAttributes();
        if (is_array($extensionAttributes) && isset($extensionAttributes['aw_coupongenerator_data'])) {
            $salesruleData = $extensionAttributes['aw_coupongenerator_data'];
        } elseif ($extensionAttributes && $extensionAttributes->getAwCoupongeneratorData()) {
            $salesruleData = $extensionAttributes->getAwCoupongeneratorData();
        } else {
            return $rule;
        }

        $salesruleDataObject = $this->salesruleFactory->create();

        $this->dataObjectHelper->populateWithArray(
            $salesruleDataObject,
            $salesruleData,
            SalesruleInterface::class
        );

        if (!$salesruleDataObject->getRuleId()) {
            $salesruleDataObject->setRuleId($rule->getRuleId());
        }

        $this->salesruleRepository->save($salesruleDataObject);

        return $rule;
    }
}
