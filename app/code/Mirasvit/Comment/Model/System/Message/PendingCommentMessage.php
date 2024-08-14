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


namespace Mirasvit\Comment\Model\System\Message;


use Magento\Framework\AuthorizationInterface;
use Magento\Framework\Notification\MessageInterface;
use Magento\Framework\UrlInterface;
use Mirasvit\Comment\Api\Data\CommentInterface;
use Mirasvit\Comment\Model\ConfigProvider;
use Mirasvit\Comment\Repository\CommentRepository;

class PendingCommentMessage implements MessageInterface
{
    const IDENTITY = 'mst_comment_pending';

    private $commentRepository;

    private $urlBuilder;

    private $authorization;

    private $configProvider;

    private $pendingCommentsCount = null;

    public function __construct(
        UrlInterface $urlBuilder,
        AuthorizationInterface $authorization,
        CommentRepository $commentRepository,
        ConfigProvider $configProvider
    ) {
        $this->urlBuilder        = $urlBuilder;
        $this->authorization     = $authorization;
        $this->commentRepository = $commentRepository;
        $this->configProvider    = $configProvider;
    }

    public function getIdentity()
    {
        return self::IDENTITY;
    }

    public function isDisplayed()
    {
        return $this->configProvider->isAdminNotificationEnabled()
            && $this->authorization->isAllowed('Mirasvit_Comment::comment_all')
            && (bool)$this->getPendingCommentsCount();
    }

    public function getText()
    {
        $message = __('Mirasvit Comments: You have %1 pending comment(s).', $this->getPendingCommentsCount());

        return $message;
    }

    public function getSeverity()
    {
        return self::SEVERITY_NOTICE;
    }

    private function getPendingCommentsCount(): int
    {
        if (is_null($this->pendingCommentsCount)) {
            $this->pendingCommentsCount = $this->commentRepository
                ->getCollection(false)
                ->addFieldToFilter(CommentInterface::STATUS, CommentInterface::STATUS_PENDING)
                ->getSize();
        }

        return (int)$this->pendingCommentsCount;
    }
}
