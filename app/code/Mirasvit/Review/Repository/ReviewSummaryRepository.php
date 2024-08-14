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
use Mirasvit\Review\Api\Data\ReviewSummaryInterface;
use Mirasvit\Review\Api\Data\ReviewSummaryInterfaceFactory;
use Mirasvit\Review\Model\ResourceModel\ReviewSummary\Collection;
use Mirasvit\Review\Model\ResourceModel\ReviewSummary\CollectionFactory;

class ReviewSummaryRepository
{
    private $entityManager;

    private $reviewFactory;

    private $collectionFactory;

    public function __construct(
        EntityManager $entityManager,
        ReviewSummaryInterfaceFactory $reviewFactory,
        CollectionFactory $collectionFactory
    ) {
        $this->entityManager     = $entityManager;
        $this->reviewFactory     = $reviewFactory;
        $this->collectionFactory = $collectionFactory;
    }

    public function create(): ReviewSummaryInterface
    {
        return $this->reviewFactory->create();
    }

    public function get(int $id): ?ReviewSummaryInterface
    {
        $model = $this->create();

        $model->load($id);

        return $model->getId() ? $model : null;
    }

    public function getCollection(): Collection
    {
        return $this->collectionFactory->create();
    }

    public function getByProductAndStore(int $productId, int $storeId = 0): ?ReviewSummaryInterface
    {
        $collection = $this->getCollection()
            ->addFieldToFilter(ReviewSummaryInterface::PRODUCT_ID, $productId)
            ->addFieldToFilter(ReviewSummaryInterface::STORE_ID, $storeId);

        $summaryReview = $collection->getFirstItem();

        return $summaryReview && $summaryReview->getId() ? $summaryReview : null;
    }

    public function delete(ReviewSummaryInterface $model): void
    {
        $model->delete();
    }

    public function save(ReviewSummaryInterface $model): ReviewSummaryInterface
    {
        $model->save();

        return $model;
    }
}
