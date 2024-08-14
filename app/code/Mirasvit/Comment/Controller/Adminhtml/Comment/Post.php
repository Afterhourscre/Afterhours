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

class Post extends CommentAbstract
{
    public const ADMIN_RESOURCE = 'Mirasvit_Comment::comment_edit';

    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $refererUrl     = $this->_redirect->getRefererUrl();

        $data = $this->getRequest()->getParams();

        if (!isset($data[CommentInterface::REL_ENTITY_TYPE]) || !isset($data[CommentInterface::REL_ENTITY_ID])) {
            $this->messageManager->addErrorMessage(
                (string)__('Can not save the answer. Related entity type or ID is not set')
            );

            return $resultRedirect->setRefererUrl($refererUrl);
        }

        $user = $this->context->getAuth()->getUser();

        $comment = $this->commentRepository->create();

        $comment->setRelatedEntityId((int)$data[CommentInterface::REL_ENTITY_ID])
            ->setRelatedEntityType($data[CommentInterface::REL_ENTITY_TYPE])
            ->setContent($data[CommentInterface::CONTENT])
            ->setNickname($user->getFirstName() . ' ' . $user->getLastName())
            ->setStatus(CommentInterface::STATUS_APPROVED)
            ->setIsAdmin(true)
            ->setStoreId(0);

        $parentId = $data[CommentInterface::PARENT_ID] ?? null;

        if ($parentId) {
            $parentComment = $this->commentRepository->get((int)$parentId);

            if (!$parentComment) {
                $this->messageManager->addErrorMessage(
                    (string)__('Can not add reply to the comment with ID %1. This comment no longer exist', $parentId)
                );

                return $resultRedirect->setRefererUrl($refererUrl);
            }

            $comment->setParentId((int)$parentId)
                ->setLevel($parentComment->getLevel() + 1)
                ->setPath($parentComment->getPath() . '/');
        } else {
            $comment->setLevel(1)->setPath('/');
        }

        $this->commentRepository->save($comment);

        $this->messageManager->addSuccessMessage(
            (string)__('Your comment is succesfully added.')
        );

        return $resultRedirect->setRefererUrl($refererUrl);
    }
}
