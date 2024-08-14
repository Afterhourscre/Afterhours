<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Helpdesk\Controller\Adminhtml\QuickResponse;

use Aheadworks\Helpdesk\Model\ResourceModel\QuickResponse\Collection;
use Magento\Framework\Controller\ResultFactory;

/**
 * Class MassDelete
 * @package Aheadworks\Helpdesk\Controller\Adminhtml\QuickResponse
 */
class MassDelete extends AbstractMassAction
{
    /**
     * @inheritdoc
     */
    protected function massAction(Collection $collection)
    {
        $deletedRecords = 0;

        foreach ($collection->getAllIds() as $quickResponseId) {
            $this->quickResponseRepository->deleteById($quickResponseId);
            $deletedRecords++;
        }

        if ($deletedRecords) {
            $this->messageManager->addSuccess(__('A total of %1 record(s) have been deleted.', $deletedRecords));
        } else {
            $this->messageManager->addSuccess(__('No records have been deleted'));
        }

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/');
    }
}
