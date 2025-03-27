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


namespace Mirasvit\Comment\Controller\Comment;


use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Store\Model\StoreManagerInterface;
use Mirasvit\Comment\Api\Data\CommentInterface;
use Mirasvit\Comment\Model\ConfigProvider;
use Mirasvit\Comment\Repository\CommentRepository;
use Psr\Log\LoggerInterface;

class Post extends Action
{
    private $commentRepository;

    private $configProvider;

    private $customerSession;

    private $storeManager;

    private $logger;

    public function __construct(
        CommentRepository $commentRepository,
        ConfigProvider $configProvider,
        Session $customerSession,
        StoreManagerInterface $storeManager,
        LoggerInterface             $logger,
        Context $context
    ) {
        $this->commentRepository = $commentRepository;
        $this->configProvider    = $configProvider;
        $this->customerSession   = $customerSession;
        $this->storeManager      = $storeManager;
        $this->logger            = $logger;

        parent::__construct($context);
    }

    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        $refererUrl = $this->_redirect->getRefererUrl();

        if (!$this->isCommentAllowed()) {
            $this->messageManager->addErrorMessage((string)__('Comments from guests are not allowed'));

            return $resultRedirect->setRefererUrl($refererUrl);
        }

        $params = $this->getRequest()->getParams();

        $comment = $this->commentRepository->create();

        $comment->setNickname($params[CommentInterface::NICKNAME])
            ->setContent($params[CommentInterface::CONTENT])
            ->setRelatedEntityType($params[CommentInterface::REL_ENTITY_TYPE])
            ->setRelatedEntityId((int)$params[CommentInterface::REL_ENTITY_ID])
            ->setIsAdmin(false)
            ->setChildrenCount(0)
            ->setPath('/')
            ->setStoreId((int)$this->storeManager->getStore()->getId())
            ->setStatus($this->configProvider->isAutoApproveComments() ? CommentInterface::STATUS_APPROVED : CommentInterface::STATUS_PENDING);

        if ($this->customerSession->isLoggedIn()) {
            $comment->setCustomerId((int)$this->customerSession->getCustomerId());
        }

        if (isset($params[CommentInterface::PARENT_ID])) {
            $comment->setParentId((int)$params[CommentInterface::PARENT_ID]);
        }

        try {
            $this->commentRepository->save($comment);

            $message = __('Thank you for posting the comment.');

            if ($comment->getStatus() == CommentInterface::STATUS_PENDING) {
                $message .= __(' Your comment will be displayed after moderation.');
            }

            $this->messageManager->addSuccessMessage((string)$message);

            return $resultRedirect->setRefererUrl($refererUrl);
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage('We can\'t save your comment right now.');

            $this->logger->error('Mirasvit_Comment: Unable to save message. ' . $e->getMessage() . $e->getTraceAsString(), [$params]);

            return $resultRedirect->setRefererUrl($refererUrl);
        }
    }

    private function isCommentAllowed(): bool
    {
        return $this->configProvider->isGuestCommentAllowed() || $this->customerSession->isLoggedIn();
    }
}
