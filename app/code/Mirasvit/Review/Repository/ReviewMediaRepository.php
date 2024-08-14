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



namespace Mirasvit\Review\Repository;


use Magento\Framework\EntityManager\EntityManager;
use Mirasvit\Review\Api\Data\ReviewMediaInterface;
use Mirasvit\Review\Api\Data\ReviewMediaInterfaceFactory;
use Mirasvit\Review\Model\ResourceModel\ReviewMedia\Collection;
use Mirasvit\Review\Model\ResourceModel\ReviewMedia\CollectionFactory;


class ReviewMediaRepository
{
    private $entityManager;

    private $mediaFactory;

    private $collectionFactory;

    public function __construct(
        EntityManager               $entityManager,
        ReviewMediaInterfaceFactory $mediaFactory,
        CollectionFactory           $collectionFactory
    ) {
        $this->entityManager     = $entityManager;
        $this->mediaFactory      = $mediaFactory;
        $this->collectionFactory = $collectionFactory;
    }

    public function create(): ReviewMediaInterface
    {
        return $this->mediaFactory->create();
    }

    public function get(int $id): ?ReviewMediaInterface
    {
        $model = $this->create();

        $model->load($id);

        return $model->getId() ? $model : null;
    }

    public function getCollection(): \Mirasvit\Review\Model\ResourceModel\ReviewMedia\Collection
    {
        return $this->collectionFactory->create();
    }

    public function getByReview(int $reviewId): ?array
    {
        $collection = $this->getCollection()
            ->addFieldToFilter(ReviewMediaInterface::REVIEW_ID, $reviewId);

        $reviewMedia = $collection->getItems();

        return count($reviewMedia) > 0 ? $reviewMedia : null;
    }

    public function getByProduct(int $productId): ?array
    {
        $collection = $this->getCollection()
            ->addFieldToFilter(ReviewMediaInterface::PRODUCT_ID, $productId);

        $reviewMedia = $collection->getItems();

        return count($reviewMedia) > 0 ? $reviewMedia : null;
    }

    public function delete(ReviewMediaInterface $model): void
    {
        $model->delete();
    }

    public function save(ReviewMediaInterface $model): ReviewMediaInterface
    {
        $model->save();

        return $model;
    }
}
