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


namespace Mirasvit\Review\Repository;


use Magento\Framework\EntityManager\EntityManager;
use Mirasvit\Review\Api\Data\ReviewInterface;
use Mirasvit\Review\Api\Data\ReviewInterfaceFactory;
use Mirasvit\Review\Model\Uploader;
use Mirasvit\Review\Model\ResourceModel\Review\Collection;
use Mirasvit\Review\Model\ResourceModel\Review\CollectionFactory;

class ReviewRepository
{
    private $entityManager;

    private $reviewFactory;

    private $collectionFactory;

    private $uploader;

    public function __construct(
        Uploader               $uploader,
        EntityManager          $entityManager,
        ReviewInterfaceFactory $reviewFactory,
        CollectionFactory      $collectionFactory
    ) {
        $this->uploader          = $uploader;
        $this->entityManager     = $entityManager;
        $this->reviewFactory     = $reviewFactory;
        $this->collectionFactory = $collectionFactory;
    }

    public function create(): ReviewInterface
    {
        return $this->reviewFactory->create();
    }

    public function get(int $id): ?ReviewInterface
    {
        $model = $this->create();

        $model->load($id);

        return $model->getId() ? $model : null;
    }

    public function getCollection(bool $onlyApproved = true): Collection
    {
        $collection = $this->collectionFactory->create();

        if ($onlyApproved) {
            $collection->addFieldToFilter(ReviewInterface::STATUS_ID, ReviewInterface::STATUS_APPROVED);
        }

        $collection->addStoreData();

        return $collection;
    }

    public function delete(ReviewInterface $model): void
    {
        $this->uploader->deleteImage((int)$model->getId());
        $model->delete();
    }

    public function save(ReviewInterface $model): ReviewInterface
    {
        $model->save();

        return $model;
    }
}
