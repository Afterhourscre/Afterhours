<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Coupongenerator\Controller\Adminhtml\Rule;

/**
 * Class MassActivate
 * @package Aheadworks\Coupongenerator\Controller\Adminhtml\Rule
 */
class MassDelete extends \Aheadworks\Coupongenerator\Controller\Adminhtml\Rule\MassAbstract
{
    /**
     * @var string
     */
    protected $errorMessage = 'Something went wrong while deleting rule(s).';

    /**
     * {@inheritdoc}
     */
    protected function massAction($collection)
    {
        $count = 0;
        /** @var \Aheadworks\Coupongenerator\Model\Salesrule $rule */
        foreach ($collection->getItems() as $rule) {
            /** @var \Magento\SalesRule\Api\Data\RuleInterface $magentoSalesRule */
            $magentoSalesRule = $this->magentoSalesRuleRepository->getById($rule->getRuleId());
            if ($magentoSalesRule->getRuleId()) {
                $this->magentoSalesRuleRepository->deleteById($magentoSalesRule->getRuleId());
                $count++;
            }
        }
        $this->messageManager->addSuccessMessage(__('A total of %1 rule(s) have been deleted', $count));
    }
}
