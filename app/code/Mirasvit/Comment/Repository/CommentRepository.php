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


namespace Mirasvit\Comment\Repository;


use Magento\Framework\EntityManager\EntityManager;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\PageCache\Model\Cache\Type as PageCache;
use Mirasvit\Comment\Api\Data\CommentInterface;
use Mirasvit\Comment\Api\Data\CommentInterfaceFactory;
use Mirasvit\Comment\Model\Comment;
use Mirasvit\Comment\Model\ResourceModel\Comment\Collection;
use Mirasvit\Comment\Model\ResourceModel\Comment\CollectionFactory;

class CommentRepository
{
    private $commentFactory;

    private $collectionFactory;

    private $entityManager;

    private $pageCache;

    public function __construct(
        CommentInterfaceFactory $commentFactory,
        CollectionFactory $collectionFactory,
        EntityManager $entityManager,
        PageCache $pageCache
    ) {
        $this->commentFactory    = $commentFactory;
        $this->collectionFactory = $collectionFactory;
        $this->entityManager     = $entityManager;
        $this->pageCache         = $pageCache;
    }

    public function create(): CommentInterface
    {
        return $this->commentFactory->create();
    }

    public function get(int $id): ?CommentInterface
    {
        $comment = $this->create();

        $this->entityManager->load($comment, $id);

        return $comment->getId() ? $comment : null;
    }

    public function getCollection(bool $onlyApproved = true): Collection
    {
        $collection = $this->collectionFactory->create();

        if ($onlyApproved) {
            $collection->addFieldToFilter(CommentInterface::STATUS, CommentInterface::STATUS_APPROVED);
        }

        return $collection;
    }

    public function getTree(string $type, int $id, int $storeId = 0, bool $onlyApproved = true): array
    {
        $tree = [];

        $comments = $this->getByEntity($type, $id, $onlyApproved)
            ->addStoreFilter($storeId)
            ->addFieldToFilter(CommentInterface::LEVEL, 1)
            ->setOrder(CommentInterface::CREATED_AT, 'desc');

        foreach ($comments as $comment) {
            $comment->setData(CommentInterface::CHILDREN, $this->getChildren($comment, $onlyApproved));

            $tree[$comment->getId()] = $comment;
        }

        return $tree;
    }

    public function getChildren(CommentInterface $comment, bool $onlyApproved = true): array
    {
        $children = [];

        if (!$comment->getChildrenCount()) {
            return $children;
        }

        $collection = $this->getCollection($onlyApproved)
            ->addStoreFilter($comment->getStoreId())
            ->addFieldToFilter(CommentInterface::PATH, ['like' => $comment->getPath() . '/%'])
            ->addFieldToFilter(CommentInterface::LEVEL, $comment->getLevel() + 1)
            ->setOrder(CommentInterface::CREATED_AT, 'desc');

        foreach ($collection as $child) {
            $child->setData(CommentInterface::CHILDREN, $this->getChildren($child, $onlyApproved));

            $children[$child->getId()] = $child;
        }

        return $children;
    }

    public function getByEntity(string $type, int $id, bool $onlyApproved = true): Collection
    {
        return $this->getCollection($onlyApproved)
            ->addFieldToFilter(CommentInterface::REL_ENTITY_TYPE, $type)
            ->addFieldToFilter(CommentInterface::REL_ENTITY_ID, $id);
    }

    public function save(CommentInterface $comment): CommentInterface
    {
        /** @var Comment $comment */
        $comment = $this->entityManager->save($comment);

        $this->updateComment($comment);
        $this->updateParentChildrenCount($comment->getParentId());
        $this->cleanRelevantCache($comment->getIdentities());

        return $this->entityManager->load($comment, $comment->getId());
    }

    public function delete(CommentInterface $comment): bool
    {
        /** @var Comment $comment */
        $parentId  = $comment->getParentId();
        $cacheTags = $comment->getIdentities();
        $result    = $this->entityManager->delete($comment);

        $this->updateParentChildrenCount($parentId);
        $this->cleanRelevantCache($cacheTags);

        return $result;
    }

    public function deleteByEntity(string $type, int $id)
    {
        // we delete only comments of level 1
        // all related children comments will be deleted by FK
        $collection = $this->getByEntity($type, $id, false)
            ->addFieldToFilter(CommentInterface::LEVEL, 1);

        foreach ($collection as $comment) {
            $this->delete($comment);
        }
    }

    private function updateComment(CommentInterface $comment)
    {
        /** @var Comment  $comment */
        $path = $comment->getId();

        if ($comment->getParentId()) {
            $parent = $this->get($comment->getParentId());

            $path = $parent->getPath() . '/' . $path;
        }

        $level = count(explode('/', (string)$path));

        $resource   = $comment->getResource();
        $connection = $resource->getConnection();

        $connection->update(
            $resource->getTable(CommentInterface::TABLE_NAME),
            [CommentInterface::PATH => $path, CommentInterface::LEVEL => $level],
            [CommentInterface::ID . ' = ? ' => $comment->getId()]
        );
    }

    private function updateParentChildrenCount(int $parentId = null)
    {
        if (!$parentId) {
            return;
        }

        /** @var Comment $parent */
        $parent = $this->get($parentId);

        if (!$parent) {
            throw new NoSuchEntityException(__('Can not update children counts. Comment with ID %1 does not exist', $parentId));
        }

        $resource   = $parent->getResource();
        $connection = $resource->getConnection();

        $tableName = $resource->getTable(CommentInterface::TABLE_NAME);

        $childrenCountQuery = 'SELECT COUNT(t2.' . CommentInterface::ID . ') FROM ' . $tableName
            . ' AS t2 WHERE t2.' . CommentInterface::PATH . ' LIKE "' . $parent->getPath() . '/%"';

        $childrenCount = $connection->query($childrenCountQuery)->fetchColumn(0);

        $connection->update(
            $tableName,
            [CommentInterface::CHILDREN_COUNT => $childrenCount],
            [CommentInterface::ID . ' = ? ' => $parentId]
        );

        return $this->updateParentChildrenCount($parent->getParentId());
    }

    private function cleanRelevantCache(array $tags)
    {
        if (count($tags)) {
            $this->pageCache->clean(
                \Zend_Cache::CLEANING_MODE_MATCHING_TAG,
                $tags
            );
        }
    }
}
