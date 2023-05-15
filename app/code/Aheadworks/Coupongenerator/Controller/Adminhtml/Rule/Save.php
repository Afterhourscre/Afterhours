<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Coupongenerator\Controller\Adminhtml\Rule;

use Magento\SalesRule\Api\Data\RuleInterface;

/**
 * Class Save
 * @package Aheadworks\Coupongenerator\Controller\Adminhtml\Rule
 */
class Save extends \Magento\Backend\App\Action
{
    /**
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Aheadworks_Coupongenerator::manage_rules';

    /**
     * @var RuleInterfaceFactory
     */
    private $magentoRuleDataFactory;

    /**
     * @var \Magento\SalesRule\Api\RuleRepositoryInterface
     */
    private $magentoSalesRuleRepository;

    /**
     * @var \Aheadworks\Coupongenerator\Model\Converter\Condition
     */
    private $conditionConverter;

    /**
     * @var \Aheadworks\Coupongenerator\Model\Converter\Salesrule
     */
    private $salesruleConverter;

    /**
     * @var \Aheadworks\Coupongenerator\Model\SalesruleRepository
     */
    private $salesruleRepository;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\SalesRule\Api\Data\RuleInterfaceFactory $magentoRuleDataFactory
     * @param \Magento\SalesRule\Api\RuleRepositoryInterface $magentoSalesRuleRepository
     * @param \Aheadworks\Coupongenerator\Model\Converter\Condition $conditionConverter
     * @param \Aheadworks\Coupongenerator\Model\Converter\Salesrule $salesruleConverter
     * @param \Aheadworks\Coupongenerator\Model\SalesruleRepository $salesruleRepository
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\SalesRule\Api\Data\RuleInterfaceFactory $magentoRuleDataFactory,
        \Magento\SalesRule\Api\RuleRepositoryInterface $magentoSalesRuleRepository,
        \Aheadworks\Coupongenerator\Model\Converter\Condition $conditionConverter,
        \Aheadworks\Coupongenerator\Model\Converter\Salesrule $salesruleConverter,
        \Aheadworks\Coupongenerator\Model\SalesruleRepository $salesruleRepository
    ) {
        $this->magentoRuleDataFactory = $magentoRuleDataFactory;
        $this->magentoSalesRuleRepository = $magentoSalesRuleRepository;
        $this->conditionConverter = $conditionConverter;
        $this ->salesruleConverter = $salesruleConverter;
        $this->salesruleRepository = $salesruleRepository;
        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            try {
                $data = $this->prepareData($data);

                $id = isset($data['rule_id']) ? $data['rule_id'] : false;

                /** @var \Magento\SalesRule\Api\Data\RuleInterface $magentoRule */
                $magentoRuleDataObject = $id
                    ? $this->magentoSalesRuleRepository->getById($id)
                    : $this->magentoRuleDataFactory->create();

                $magentoRuleDataObject = $this->salesruleConverter->populateWithFormData(
                    $magentoRuleDataObject,
                    $data
                );

                $magentoRuleDataObject->setCouponType(RuleInterface::COUPON_TYPE_SPECIFIC_COUPON);
                $magentoRuleDataObject->setUseAutoGeneration(true);

                $magentoRuleDataObject = $this->magentoSalesRuleRepository->save($magentoRuleDataObject);

                $this->messageManager->addSuccessMessage(__('Rule was successfully saved'));

                if ($this->getRequest()->getParam('back')) {
                    /** @var \Aheadworks\Coupongenerator\Api\Data\SalesruleInterface $salesruleDataObject */
                    $salesruleDataObject = $this->salesruleRepository->getByRuleId($magentoRuleDataObject->getRuleId());
                    return $resultRedirect->setPath('*/*/edit', ['id' => $salesruleDataObject->getId()]);
                }
                return $resultRedirect->setPath('*/*/index');
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the rule data'));
            }
        }
        return $resultRedirect->setPath('*/*/index');
    }

    /**
     * Prepare data before save
     *
     * @param array $data
     * @return array
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    private function prepareData(array $data)
    {
        if (isset($data['simple_action'])
            && $data['simple_action'] == 'by_percent'
            && isset($data['discount_amount'])
        ) {
            $data['discount_amount'] = min(100, $data['discount_amount']);
        }
        if (isset($data['product_ids']) && !is_array($data['product_ids'])) {
            $data['product_ids'] = null;
        }
        if (isset($data['rule']['conditions'])) {
            $conditionArray = $this->convertFlatToRecursive($data['rule'], ['conditions']);
            if (is_array($conditionArray['conditions'][1])) {
                $data['condition'] = $this->conditionConverter
                    ->arrayToDataModel($conditionArray['conditions'][1]);
            } else {
                $data['condition'] = '';
            }
        }
        if (isset($data['rule']['actions'])) {
            $conditionArray = $this->convertFlatToRecursive($data['rule'], ['actions']);
            if (is_array($conditionArray['actions'][1])) {
                $data['action_condition'] = $this->conditionConverter
                    ->arrayToDataModel($conditionArray['actions'][1]);
            } else {
                $data['action_condition'] = '';
            }
        }
        unset($data['rule']);

        return $data;
    }

    /**
     * Get conditions data recursively
     *
     * @param array $data
     * @param array $allowedKeys
     * @return array
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    private function convertFlatToRecursive(array $data, $allowedKeys = [])
    {
        $result = [];
        foreach ($data as $key => $value) {
            if (in_array($key, $allowedKeys) && is_array($value)) {
                foreach ($value as $id => $data) {
                    $path = explode('--', $id);
                    $node = & $result;

                    for ($i = 0, $l = sizeof($path); $i < $l; $i++) {
                        if (!isset($node[$key][$path[$i]])) {
                            $node[$key][$path[$i]] = [];
                        }
                        $node = & $node[$key][$path[$i]];
                    }

                    foreach ($data as $k => $v) {
                        // Fix for magento UI form, if empty value in array exist
                        if (is_array($v)) {
                            foreach ($v as $dk => $dv) {
                                if (empty($dv)) {
                                    unset($v[$dk]);
                                }
                            }
                            if (!count($v)) {
                                continue;
                            }
                        }

                        $node[$k] = $v;
                    }
                }
            }
        }

        return $result;
    }
}
