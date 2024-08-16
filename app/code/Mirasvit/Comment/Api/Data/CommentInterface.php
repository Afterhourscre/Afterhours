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


namespace Mirasvit\Comment\Api\Data;


interface CommentInterface
{
    const TABLE_NAME = 'mst_comment_comment';

    const ID              = 'comment_id';
    const PARENT_ID       = 'parent_id';
    const REL_ENTITY_ID   = 'rel_entity_id';
    const REL_ENTITY_TYPE = 'rel_entity_type';
    const NICKNAME        = 'nickname';
    const STATUS          = 'status';
    const CONTENT         = 'content';
    const CUSTOMER_ID     = 'customer_id';
    const IS_ADMIN        = 'is_admin';
    const STORE_ID        = 'store_id';
    const PATH            = 'path';
    const LEVEL           = 'level';
    const CHILDREN_COUNT  = 'children_count';
    const CHILDREN        = 'children';
    const CREATED_AT      = 'created_at';
    const UPDATED_AT      = 'updated_at';

    const STATUS_PENDING  = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    const COMMENT_STATUSES = [
        self::STATUS_PENDING,
        self::STATUS_APPROVED,
        self::STATUS_REJECTED
    ];

    const REL_ENTITY_TYPE_REVIEW    = 'review';
    const REL_ENTITY_TYPE_BLOG_POST = 'blog_post';

    public function getParentId(): ?int;

    public function setParentId(int $value): self;

    public function getRelatedEntityId(): int;

    public function setRelatedEntityId(int $value): self;

    public function getRelatedEntityType(): string;

    public function setRelatedEntityType(string $value): self;

    public function getNickname(): string;

    public function setNickname(string $value): self;

    public function getStatus(): string;

    public function setStatus(string $value): self;

    public function getContent(): string;

    public function setContent(string $value): self;

    public function getCustomerId(): ?int;

    public function setCustomerId(int $value): self;

    public function getIsAdmin(): bool;

    public function setIsAdmin(bool $value): self;

    public function getStoreId(): int;

    public function setStoreId(int $value): self;

    public function getPath(): string;

    public function setPath(string $value): self;

    public function getLevel(): int;

    public function setLevel(int $value): self;

    public function getChildrenCount(): int;

    public function setChildrenCount(int $value): self;

    public function getChildren(): array;

    public function getCreatedAt(): string;

    public function getUpdatedAt(): string;
}
