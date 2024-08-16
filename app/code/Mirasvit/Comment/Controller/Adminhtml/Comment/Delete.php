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
 * @package   mirasvit/module-comment
 * @version   1.0.1
 * @copyright Copyright (C) 2024 Mirasvit (https://mirasvit.com/)
 */


declare(strict_types=1);


namespace Mirasvit\Comment\Controller\Adminhtml\Comment;


use Mirasvit\Comment\Api\Data\CommentInterface;
use Mirasvit\Comment\Controller\Adminhtml\CommentAbstract;

class Delete extends CommentAbstract
{
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $refererUrl     = $this->_redirect->getRefererUrl();

        $commentId = (int)$this->getRequest()->getParam(CommentInterface::ID);

        if (!$commentId) {
            $this->messageManager->addErrorMessage((string)__('Please select the comment to delete'));

            return $resultRedirect->setRefererUrl($refererUrl);
        }

        $comment = $this->commentRepository->get((int)$commentId);

        if (!$comment) {
            $this->messageManager->addErrorMessage((string)__('Comment with ID %1 does not exist', $commentId));

            return $resultRedirect->setRefererUrl($refererUrl);
        }

        $this->commentRepository->delete($comment);

        $this->messageManager->addSuccessMessage('Comment was deleted successfuly');

        return $resultRedirect->setRefererUrl($refererUrl);
    }
}
