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

use Magento\Framework\Controller\ResultFactory;
use Mirasvit\Review\Api\Data\ReviewInterface;
use Mirasvit\Review\Controller\Adminhtml\ReviewAbstract;

class Edit extends ReviewAbstract
{
    public function execute()
    {
        $model = $this->initModel();
        $id    = (int)$this->getRequest()->getParam(ReviewInterface::ID);

        if ($id && !$model) {
            $this->messageManager->addErrorMessage((string)__('This review no longer exists.'));
            $resultRedirect = $this->resultRedirectFactory->create();

            return $resultRedirect->setPath('*/*/');
        }

        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);

        $this->initPage($resultPage)
            ->getConfig()->getTitle()->prepend(
                $model->getId()
                    ? (string)__('Review "%1"', $model->getTitle())
                    : (string)__('New Review')
            );

        return $resultPage;
    }
}
