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


namespace Mirasvit\Comment\Model;


use Magento\Framework\App\ObjectManager;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;
use Mirasvit\Comment\Api\Data\CommentInterface;
use Mirasvit\Comment\Model\ResourceModel\Comment as CommentResource;

class Comment extends AbstractModel implements IdentityInterface, CommentInterface
{
    const ENTITY    = 'mst_comment';

    protected $_eventPrefix = 'mst_comment';

    public function getIdentities()
    {
        $cacheTags = [];

        switch ($this->getRelatedEntityType()) {
            case self::REL_ENTITY_TYPE_BLOG_POST:
                $cacheTags[] = 'blog_post_' . $this->getRelatedEntityId();

                break;
            case self::REL_ENTITY_TYPE_REVIEW:
                if (class_exists('\Mirasvit\Review\Repository\ReviewRepository')) {
                    $reviewRepository = ObjectManager::getInstance()->get('\Mirasvit\Review\Repository\ReviewRepository');

                    if ($review = $reviewRepository->get($this->getRelatedEntityId())) {
                        $cacheTags = array_merge($cacheTags, $review->getIdentities());
                    }
                }

                break;
            default:
                break;
        }

        return $cacheTags;
    }

    protected function _construct()
    {
        $this->_init(CommentResource::class);
    }

    public function getId()
    {
        return (int)$this->getData(self::ID);
    }

    public function getParentId(): ?int
    {
        $parentId = $this->getData(self::PARENT_ID);

        return $parentId ? (int)$parentId : null;
    }

    public function setParentId(int $value): CommentInterface
    {
        return $this->setData(self::PARENT_ID, $value);
    }

    public function getRelatedEntityId(): int
    {
        return (int)$this->getData(self::REL_ENTITY_ID);
    }

    public function setRelatedEntityId(int $value): CommentInterface
    {
        return $this->setData(self::REL_ENTITY_ID, $value);
    }

    public function getRelatedEntityType(): string
    {
        return (string)$this->getData(self::REL_ENTITY_TYPE);
    }

    public function setRelatedEntityType(string $value): CommentInterface
    {
        return $this->setData(self::REL_ENTITY_TYPE, $value);
    }

    public function getNickname(): string
    {
        return (string)$this->getData(self::NICKNAME);
    }

    public function setNickname(string $value): CommentInterface
    {
        return $this->setData(self::NICKNAME, $value);
    }

    public function getStatus(): string
    {
        return (string)$this->getData(self::STATUS);
    }

    public function setStatus(string $value): CommentInterface
    {
        return $this->setData(self::STATUS, $value);
    }

    public function getContent(): string
    {
        return (string)$this->getData(self::CONTENT);
    }

    public function setContent(string $value): CommentInterface
    {
        return $this->setData(self::CONTENT, $value);
    }

    public function getCustomerId(): ?int
    {
        $customerId = $this->getData(self::CUSTOMER_ID);

        return $customerId ? (int)$customerId : null;
    }

    public function setCustomerId(int $value): CommentInterface
    {
        return $this->setData(self::CUSTOMER_ID, $value);
    }

    public function getIsAdmin(): bool
    {
        return (bool)$this->getData(self::IS_ADMIN);
    }

    public function setIsAdmin(bool $value): CommentInterface
    {
        return $this->setData(self::IS_ADMIN, $value);
    }

    public function getStoreId(): int
    {
        return (int)$this->getData(self::STORE_ID);
    }

    public function setStoreId(int $value): CommentInterface
    {
        return $this->setData(self::STORE_ID, $value);
    }

    public function getCreatedAt(): string
    {
        return $this->getData(self::CREATED_AT);
    }

    public function getUpdatedAt(): string
    {
        return $this->getData(self::UPDATED_AT);
    }

    public function getPath(): string
    {
        return $this->getData(self::PATH);
    }

    public function setPath(string $value): CommentInterface
    {
        return $this->setData(self::PATH, $value);
    }

    public function getLevel(): int
    {
        return (int)$this->getData(self::LEVEL);
    }

    public function setLevel(int $value): CommentInterface
    {
        return $this->setData(self::LEVEL, $value);
    }

    public function getChildrenCount(): int
    {
        return (int)$this->getData(self::CHILDREN_COUNT);
    }

    public function setChildrenCount(int $value): CommentInterface
    {
        return $this->setData(self::CHILDREN_COUNT, $value);
    }

    public function getChildren(): array
    {
        return $this->getData(self::CHILDREN) ?: [];
    }
}
