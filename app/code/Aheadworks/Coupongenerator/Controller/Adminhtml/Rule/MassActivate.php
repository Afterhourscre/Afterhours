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
class MassActivate extends \Aheadworks\Coupongenerator\Controller\Adminhtml\Rule\MassAbstract
{
    /**
     * @var string
     */
    protected $errorMessage = 'Something went wrong while activating rule(s).';

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
                $magentoSalesRule->setIsActive(\Aheadworks\Coupongenerator\Model\Source\Rule\Status::STATUS_ACTIVE);
                $this->magentoSalesRuleRepository->save($magentoSalesRule);
                $count++;
            }
        }
        $this->messageManager->addSuccessMessage(__('A total of %1 rule(s) have been activated', $count));
    }
}
