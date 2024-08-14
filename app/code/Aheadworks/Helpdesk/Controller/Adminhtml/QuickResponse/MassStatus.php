<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Helpdesk\Controller\Adminhtml\QuickResponse;

use Aheadworks\Helpdesk\Model\ResourceModel\QuickResponse\Collection;
use Magento\Framework\Controller\ResultFactory;
use Aheadworks\Helpdesk\Api\Data\QuickResponseInterface;

/**
 * Class MassStatus
 * @package Aheadworks\Helpdesk\Controller\Adminhtml\QuickResponse
 */
class MassStatus extends AbstractMassAction
{
    /**
     * @inheritdoc
     */
    protected function massAction(Collection $collection)
    {
        $status = (int) $this->getRequest()->getParam('status');
        $changedRecords = 0;

        foreach ($collection->getAllIds() as $quickResponseId) {
            try {
                $quickResponseModel = $this->quickResponseRepository->get($quickResponseId);
            } catch (\Exception $e) {
                $quickResponseModel = null;
            }
            if ($quickResponseModel) {
                $quickResponseModel->setData(QuickResponseInterface::IS_ACTIVE, $status);
                $this->quickResponseRepository->save($quickResponseModel);
                $changedRecords++;
            }
        }

        if ($changedRecords) {
            $this->messageManager->addSuccess(__('A total of %1 record(s) have been changed.', $changedRecords));
        } else {
            $this->messageManager->addSuccess(__('No records have been changed'));
        }

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/');
    }
}
