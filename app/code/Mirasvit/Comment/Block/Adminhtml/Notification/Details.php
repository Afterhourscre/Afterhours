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


namespace Mirasvit\Comment\Block\Adminhtml\Notification;


use Magento\Backend\Block\Template;
use Magento\Framework\Data\Collection;
use Mirasvit\Comment\Api\Data\CommentInterface;
use Mirasvit\Comment\Repository\CommentRepository;

class Details extends Template
{
    protected $_template = "Mirasvit_Comment::notification/detail.phtml";

    private $commentRepository;

    private $pendingCommentsData = [];

    /** @var int|null */
    private $pendingCommentsCount = null;

    public function __construct(
        CommentRepository $commentRepository,
        Template\Context $context,
        array $data = []
    ) {
        $this->commentRepository = $commentRepository;

        parent::__construct($context, $data);
    }

    public function getPendingCommentsData(): array
    {
        if (!count($this->pendingCommentsData)) {
            $data = [
                CommentInterface::REL_ENTITY_TYPE_BLOG_POST => [],
                CommentInterface::REL_ENTITY_TYPE_REVIEW    => [],
            ];

            $this->pendingCommentsCount = 0;

            $pendingCollection = $this->commentRepository->getCollection(false)
                ->addFieldToFilter(CommentInterface::STATUS, CommentInterface::STATUS_PENDING)
                ->addOrder(CommentInterface::REL_ENTITY_ID, Collection::SORT_ORDER_ASC);

            /** @var CommentInterface $comment */
            foreach ($pendingCollection as $comment) {
                if (!isset($data[$comment->getRelatedEntityType()][$comment->getRelatedEntityId()])) {
                    $data[$comment->getRelatedEntityType()][$comment->getRelatedEntityId()] = 0;
                }

                $data[$comment->getRelatedEntityType()][$comment->getRelatedEntityId()]++;

                $this->pendingCommentsCount++;
            }

            $this->pendingCommentsData = $data;
        }

        return $this->pendingCommentsData;
    }

    public function getPendingCommentsCount(): ?int
    {
        if (is_null($this->pendingCommentsCount)) {
            $this->getPendingCommentsData();
        }

        return $this->pendingCommentsCount;
    }

    public function getCommentEntityLink(string $type, int $id): string
    {
        switch ($type) {
            case CommentInterface::REL_ENTITY_TYPE_BLOG_POST:
                return $this->getUrl('blog/post/edit', ['post_id' => $id]);
            case CommentInterface::REL_ENTITY_TYPE_REVIEW:
                return $this->getUrl('mst_review/review/edit', ['review_id' => $id]);
            default:
                return '';
        }
    }

    public function getEntityLabel(string $type): string
    {
        switch ($type) {
            case CommentInterface::REL_ENTITY_TYPE_BLOG_POST:
                return (string)__('Blog Post');
            case CommentInterface::REL_ENTITY_TYPE_REVIEW:
                return (string)__('Product Review');
            default:
                return (string)__('Other');
        }
    }
}
