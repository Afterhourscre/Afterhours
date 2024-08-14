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

class Update extends CommentAbstract
{
    public const ADMIN_RESOURCE = 'Mirasvit_Comment::comment_edit';

    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $refererUrl     = $this->_redirect->getRefererUrl();

        $commentId = (int)$this->getRequest()->getParam(CommentInterface::ID);

        if (!$commentId) {
            $this->messageManager->addErrorMessage((string)__('Please select the comment to change status'));

            return $resultRedirect->setRefererUrl($refererUrl);
        }

        $comment = $this->commentRepository->get((int)$commentId);

        if (!$comment) {
            $this->messageManager->addErrorMessage((string)__('Comment with ID %1 does not exist', $commentId));

            return $resultRedirect->setRefererUrl($refererUrl);
        }

        $status = $this->getRequest()->getParam(CommentInterface::STATUS);

        if (
            !$status
            || !in_array($status, CommentInterface::COMMENT_STATUSES)
        ) {
            $this->messageManager->addErrorMessage((string)__(
                'Unable to update comment with ID %1. Invalid status "%2"',
                $commentId,
                (string)$status
            ));

            return $resultRedirect->setRefererUrl($refererUrl);
        }

        if ($status == $comment->getStatus()) {
            $this->messageManager->addNoticeMessage((string)__(
                'Comment with ID %1 already has the status "%2"',
                $commentId,
                $status
            ));

            return $resultRedirect->setRefererUrl($refererUrl);
        }

        $comment->setStatus($status);

        $this->commentRepository->save($comment);

        $this->messageManager->addSuccessMessage((string)__(
            'Status of the comment with ID %1 was updated successfully',
            $commentId
        ));

        return $resultRedirect->setRefererUrl($refererUrl);
    }
}
