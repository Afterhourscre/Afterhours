<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Controller\Adminhtml\Rule;

use Aheadworks\OnSale\Model\ResourceModel\Rule\Collection;
use Magento\Framework\Controller\ResultFactory;

/**
 * Class MassStatus
 *
 * @package Aheadworks\OnSale\Controller\Adminhtml\Rule
 */
class MassStatus extends AbstractMassAction
{
    /**
     * @inheritdoc
     */
    protected function massAction(Collection $collection)
    {
        $status = (bool) $this->getRequest()->getParam('status');
        $updatedRecords = 0;

        foreach ($collection->getAllIds() as $ruleId) {
            $rule = $this->ruleRepository->get($ruleId);
            $rule->setIsActive($status);
            $this->ruleRepository->save($rule);
            $updatedRecords++;
        }

        if ($updatedRecords) {
            $this->messageManager->addSuccessMessage(__('A total of %1 record(s) have been updated.', $updatedRecords));
        } else {
            $this->messageManager->addSuccessMessage(__('No records have been updated.'));
        }

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/');
    }
}
