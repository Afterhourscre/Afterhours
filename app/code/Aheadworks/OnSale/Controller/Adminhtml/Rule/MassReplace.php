<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Controller\Adminhtml\Rule;

use Aheadworks\OnSale\Api\Data\RuleInterface;
use Aheadworks\OnSale\Model\ResourceModel\Rule\Collection;
use Magento\Framework\Controller\ResultFactory;

/**
 * Class MassReplace
 *
 * @package Aheadworks\OnSale\Controller\Adminhtml\Rule
 */
class MassReplace extends AbstractMassAction
{
    /**
     * {@inheritdoc}
     */
    protected function massAction(Collection $collection)
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        $labelId = (int)$this->getRequest()->getParam('label_id');
        $updatedRecords = 0;
        if ($labelId) {
            foreach ($collection->getAllIds() as $ruleId) {
                /** @var RuleInterface $rule */
                $rule = $this->ruleRepository->get($ruleId);
                $rule->setLabelId($labelId);
                $this->ruleRepository->save($rule);
                $updatedRecords++;
            }
        }

        if ($updatedRecords) {
            $this->messageManager->addSuccessMessage(__('A total of %1 record(s) have been updated.', $updatedRecords));
        } else {
            $this->messageManager->addSuccessMessage(__('No records have been updated.'));
        }

        return $resultRedirect->setPath('*/*/');
    }
}
