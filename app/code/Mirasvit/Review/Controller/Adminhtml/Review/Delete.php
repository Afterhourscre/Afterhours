<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-review
 * @version   1.1.2
 * @copyright Copyright (C) 2024 Mirasvit (https://mirasvit.com/)
 */


declare(strict_types=1);


namespace Mirasvit\Review\Controller\Adminhtml\Review;


use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\ForwardFactory;
use Magento\Framework\Registry;
use Magento\Review\Model\RatingFactory;
use Mirasvit\Review\Api\Data\ReviewInterface;
use Mirasvit\Review\Controller\Adminhtml\ReviewAbstract;
use Mirasvit\Review\Repository\ReviewRepository;

class Delete extends ReviewAbstract
{
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();

        if ($reviewId = $this->getRequest()->getParam(ReviewInterface::ID)) {
            $model = $this->initModel();

            if (!$model->getId()) {
                $this->messageManager->addErrorMessage((string)__('This review no longer exists.'));

                return $resultRedirect->setPath('*/*/');
            }

            try {
                $this->reviewRepository->delete($model);
                $this->messageManager->addSuccessMessage((string)__('The review has been deleted.'));
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }

            return $resultRedirect->setPath('*/*/');
        }

        $this->messageManager->addErrorMessage((string)__('This review no longer exists.'));

        return $resultRedirect->setPath('*/*/');
    }
}
