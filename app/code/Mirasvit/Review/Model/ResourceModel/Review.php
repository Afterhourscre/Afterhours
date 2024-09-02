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


namespace Mirasvit\Review\Model\ResourceModel;

use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Review\Model\Rating;
use Magento\Review\Model\Rating\Option\VoteFactory;
use Mirasvit\Core\Service\SerializeService;
use Mirasvit\Review\Service\VerifyingService;
use Magento\Review\Model\RatingFactory;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Mirasvit\Review\Api\Data\ReviewInterface;
use Mirasvit\Review\Api\Data\ReviewMediaInterface;
use Mirasvit\Review\Model\Uploader;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.TooManyFields)
 */
class Review extends AbstractDb
{
    /**
     * @var string
     */
    protected $reviewTable;

    /**
     * @var string
     */
    protected $reviewDetailTable;

    /**
     * @var string
     */
    protected $reviewStatusTable;

    /**
     * @var string
     */
    protected $reviewEntityTable;

    /**
     * @var string
     */
    protected $reviewStoreTable;

    /**
     * @var string
     */
    protected $aggregateTable;

    /**
     * @var string
     */
    protected $reviewAdditionalTable;

    /**
     * @var string
     */
    protected $reviewMediaTable;

    /**
     * @var string
     */
    protected $reviewAttachmentTable;

    protected $ratingFactory;

    protected $voteFactory;

    protected $ratingOptions;

    protected $date;

    protected $storeManager;

    protected $remoteAddress;

    protected $uploader;

    public function __construct(
        Uploader                                          $uploader,
        RemoteAddress                                     $remoteAddress,
        RatingFactory                                     $ratingFactory,
        VoteFactory                                       $voteFactory,
        \Magento\Review\Model\ResourceModel\Rating\Option $ratingOptions,
        \Magento\Framework\Stdlib\DateTime\DateTime       $date,
        \Magento\Store\Model\StoreManagerInterface        $storeManager,
        Context                                           $context,
        string                                            $connectionName = null
    ) {
        $this->uploader      = $uploader;
        $this->remoteAddress = $remoteAddress;
        $this->ratingFactory = $ratingFactory;
        $this->voteFactory   = $voteFactory;
        $this->ratingOptions = $ratingOptions;
        $this->date          = $date;
        $this->storeManager  = $storeManager;

        parent::__construct($context, $connectionName);
    }

    protected function _construct()
    {
        $this->_init(ReviewInterface::MAIN_TABLE, ReviewInterface::ID);

        $this->reviewTable           = $this->getTable(ReviewInterface::MAIN_TABLE);
        $this->reviewDetailTable     = $this->getTable(ReviewInterface::DETAIL_TABLE);
        $this->reviewStatusTable     = $this->getTable(ReviewInterface::STATUS_TABLE);
        $this->reviewEntityTable     = $this->getTable(ReviewInterface::ENTITY_TABLE);
        $this->reviewStoreTable      = $this->getTable(ReviewInterface::STORE_TABLE);
        $this->aggregateTable        = $this->getTable(ReviewInterface::SUMMARY_TABLE);
        $this->reviewAdditionalTable = $this->getTable(ReviewInterface::DETAIL_ADDITIONAL_TABLE);
        $this->reviewMediaTable      = $this->getTable(ReviewMediaInterface::TABLE_NAME);
    }

    /**
     * {@inheritDoc}
     */
    protected function _getLoadSelect($field, $value, $object)
    {
        $select = parent::_getLoadSelect($field, $value, $object);

        $select->join(
            $this->reviewDetailTable,
            $this->reviewTable . '.' . ReviewInterface::ID . ' = ' . $this->reviewDetailTable . '.' . ReviewInterface::ID
        )->joinLeft(
            $this->reviewAdditionalTable,
            $this->reviewTable . '.' . ReviewInterface::ID . ' = ' . $this->reviewAdditionalTable . '.' . ReviewInterface::ID,
            [
                ReviewInterface::PROS,
                ReviewInterface::CONS,
                ReviewInterface::IP,
                ReviewInterface::COUNTRY,
                ReviewInterface::COUNTRY_ISO,
                ReviewInterface::LOCATION,
                ReviewInterface::PRODUCT_IDS,
                ReviewInterface::PRODUCT_INFO,
                ReviewInterface::IS_VERIFIED_BUYER,
                ReviewInterface::REINDEXED_AT
            ]
        )->join(
            $this->reviewStoreTable,
            $this->reviewTable . '.' . ReviewInterface::ID . ' = ' . $this->reviewStoreTable . '.' . ReviewInterface::ID,
            ['stores' => 'GROUP_CONCAT(' . $this->reviewStoreTable . '.' . ReviewInterface::STORE_ID . ')']
        );

        return $select;
    }

    protected function _afterSave(\Magento\Framework\Model\AbstractModel $object)
    {
        $connection = $this->getConnection();
        /**
         * save detail
         */
        $detail   = [
            ReviewInterface::TITLE    => $object->getTitle(),
            ReviewInterface::DETAIL   => $object->getDetail(),
            ReviewInterface::NICKNAME => $object->getNickname(),
        ];
        $select   = $connection->select()
            ->from($this->reviewDetailTable, 'detail_id')
            ->where('review_id = :review_id');
        $detailId = $connection->fetchOne($select, [':review_id' => $object->getId()]);

        if ($detailId) {
            $condition = ["detail_id = ?" => $detailId];
            $connection->update($this->reviewDetailTable, $detail, $condition);
        } else {
            $detail[ReviewInterface::STORE_ID]    = $object->getStoreId();
            $detail[ReviewInterface::CUSTOMER_ID] = $object->getCustomerId();
            $detail[ReviewInterface::ID]          = $object->getId();
            $connection->insert($this->reviewDetailTable, $detail);
        }

        /**
         * save stores
         */
        $stores = $object->getStores();

        if (!empty($stores)) {
            $condition = ['review_id = ?' => $object->getId()];
            $connection->delete($this->reviewStoreTable, $condition);

            if (!in_array(0, $stores)) {
                $stores[] = 0;
            }

            $insertedStoreIds = [];
            foreach ($stores as $storeId) {
                if (in_array($storeId, $insertedStoreIds)) {
                    continue;
                }
                $insertedStoreIds[] = $storeId;
                $storeInsert        = ['store_id' => $storeId, 'review_id' => $object->getId()];
                $connection->insert($this->reviewStoreTable, $storeInsert);
            }
        }

        $this->saveAdditionalDetails($object);

        if ($object->getData('updateMedia')) {
            $this->saveReviewMedia($object);
        }

        // reaggregate ratings, that depend on this review
        $this->_aggregateRatings($this->_loadVotedRatingIds($object->getId()), $object->getEntityPkValue());

        return $this;
    }

    private function saveAdditionalDetails(\Magento\Framework\Model\AbstractModel $object): void
    {
        $connection = $this->getConnection();

        $additional = [
            ReviewInterface::CONS              => $this->cleanupAdditionalField($object->getCons()),
            ReviewInterface::PROS              => $this->cleanupAdditionalField($object->getPros()),
            ReviewInterface::IP                => $object->getIp() ? : $this->remoteAddress->getRemoteAddress(),
            ReviewInterface::COUNTRY           => $object->getCountry() ?? null,
            ReviewInterface::COUNTRY_ISO       => $object->getCountryIso() ?? null,
            ReviewInterface::LOCATION          => $object->getLocation() ?? null,
            ReviewInterface::PRODUCT_IDS       => $object->getProductIds() ? implode(',', $object->getProductIds()) : null,
            ReviewInterface::IS_VERIFIED_BUYER => $object->getIsVerifiedBuyer() ?? false,
            ReviewInterface::PRODUCT_INFO      => is_array($object->getProductInfo()) ? SerializeService::encode($object->getProductInfo()) : null,
            ReviewInterface::REINDEXED_AT      => $object->getReindexedAt() ?? null,
        ];

        $select = $connection->select()
            ->from($this->reviewAdditionalTable, ReviewInterface::ID)
            ->where('review_id = :review_id');

        if ($connection->fetchOne($select, [':review_id' => $object->getId()])) {
            $condition = ["review_id = ?" => $object->getId()];
            $connection->update($this->reviewAdditionalTable, $additional, $condition);
        } else {
            $additional[ReviewInterface::ID] = $object->getId();
            $connection->insert($this->reviewAdditionalTable, $additional);
        }
    }

    private function cleanupAdditionalField(string $text = null): ?string
    {
        if (is_null($text) || !trim($text)) {
            return null;
        }

        $rows     = explode(PHP_EOL, trim($text));
        $prepared = array_filter($rows, 'trim');

        return implode(PHP_EOL, $prepared);
    }

    public function saveReviewMedia(\Magento\Framework\Model\AbstractModel $object)
    {
        $connection = $this->getConnection();
        $newMedia   = [];

        $select = $connection->select()
            ->from($this->reviewMediaTable, ReviewMediaInterface::REVIEW_ID)
            ->where('review_id = :review_id');

        if ($mediaData = $object->getMedia()) {

            foreach ($mediaData as $media) {
                $newMedia[] = $media[ReviewMediaInterface::VALUE];
            }

            $this->uploader->deleteImage((int)$object->getId(), $newMedia);

            $connection->delete($this->reviewMediaTable, ["review_id = ?" => $object->getId()]);


            foreach ($mediaData as $media) {
                $media[ReviewMediaInterface::REVIEW_ID] = $object->getId();

                $connection->insert($this->reviewMediaTable, $media);
            }

        } else {
            $this->uploader->deleteImage((int)$object->getId(), $newMedia);

            $connection->delete($this->reviewMediaTable, ["review_id = ?" => $object->getId()]);
        }
    }


    public function getTotalReviews(int $productId, bool $approvedOnly = false, int $storeId = 0): int
    {
        $connection = $this->getConnection();
        $select     = $connection->select()->from(
            $this->reviewTable,
            ['review_count' => new \Zend_Db_Expr('COUNT(*)')]
        )->where(
            "{$this->reviewTable}.entity_pk_value = :pk_value"
        );
        $bind       = [':pk_value' => $productId];
        if ($storeId > 0) {
            $select->join(
                ['store' => $this->reviewStoreTable],
                $this->reviewTable . '.review_id=store.review_id AND store.store_id = :store_id',
                []
            );
            $bind[':store_id'] = (int)$storeId;
        }
        if ($approvedOnly) {
            $select->where("{$this->reviewTable}.status_id = :status_id");
            $bind[':status_id'] = ReviewInterface::STATUS_APPROVED;
        }

        return (int)$connection->fetchOne($select, $bind);
    }

    public function aggregate(ReviewInterface $object): void
    {
        if (!$object->getProductId() && $object->getId()) {
            $object->load($object->getId());
        }

        $ratingModel     = $this->ratingFactory->create();
        $ratingSummaries = $ratingModel->getEntitySummary($object->getProductId(), false);

        foreach ($ratingSummaries as $ratingSummaryObject) {
            $this->aggregateReviewSummary($object, $ratingSummaryObject);
        }
    }

    protected function aggregateReviewSummary(ReviewInterface $object, Rating $ratingSummaryObject)
    {
        $connection = $this->getConnection();

        if ($ratingSummaryObject->getCount()) {
            $ratingSummary = round($ratingSummaryObject->getSum() / $ratingSummaryObject->getCount());
        } else {
            $ratingSummary = $ratingSummaryObject->getSum();
        }

        $reviewsCount = $this->getTotalReviews(
            $object->getProductId(),
            true,
            (int)$ratingSummaryObject->getStoreId()
        );
        $select       = $connection->select()->from($this->aggregateTable)
            ->where('entity_pk_value = :pk_value')
            ->where('store_id = :store_id')
            ->where('entity_type = :entity_type');
        $bind         = [
            ':pk_value'    => $object->getProductId(),
            ':store_id'    => $ratingSummaryObject->getStoreId(),
            ':entity_type' => $object->getEntityId(),
        ];
        $oldData      = $connection->fetchRow($select, $bind) ? : [];
        $data         = new \Magento\Framework\DataObject();

        $data->setReviewsCount($reviewsCount)
            ->setEntityPkValue($object->getProductId())
            ->setEntityType($object->getEntityId())
            ->setRatingSummary($ratingSummary > 0 ? $ratingSummary : 0)
            ->setStoreId($ratingSummaryObject->getStoreId());

        $this->writeReviewSummary($oldData, $data);
    }

    protected function writeReviewSummary(array $oldData, \Magento\Framework\DataObject $data)
    {
        $connection = $this->getConnection();
        $connection->beginTransaction();
        try {
            if (isset($oldData['primary_id']) && $oldData['primary_id'] > 0) {
                $condition = ["{$this->aggregateTable}.primary_id = ?" => $oldData['primary_id']];
                $connection->update($this->aggregateTable, $data->getData(), $condition);
            } else {
                $connection->insert($this->aggregateTable, $data->getData());
            }
            $connection->commit();
        } catch (\Exception $e) {
            $connection->rollBack();
        }
    }

    /**
     * Get rating IDs from review votes
     *
     * @param int $reviewId
     *
     * @return array
     */
    protected function _loadVotedRatingIds($reviewId)
    {
        $connection = $this->getConnection();
        if (empty($reviewId)) {
            return [];
        }
        $select = $connection->select()->from(['v' => $this->getTable('rating_option_vote')], 'r.rating_id')
            ->joinInner(['r' => $this->getTable('rating')], 'v.rating_id=r.rating_id')
            ->where('v.review_id = :revire_id');

        return $connection->fetchCol($select, [':revire_id' => $reviewId]);
    }

    /**
     * Aggregate this review's ratings.
     * Useful, when changing the review.
     *
     * @param int[] $ratingIds
     * @param int   $entityPkValue
     *
     * @return \Magento\Review\Model\ResourceModel\Review
     */
    protected function _aggregateRatings($ratingIds, $entityPkValue)
    {
        if ($ratingIds && !is_array($ratingIds)) {
            $ratingIds = [(int)$ratingIds];
        }
        if ($ratingIds && $entityPkValue) {
            foreach ($ratingIds as $ratingId) {
                $this->ratingOptions->aggregateEntityByRatingId($ratingId, $entityPkValue);
            }
        }

        return $this;
    }

    /**
     * Reaggregate this review's ratings.
     *
     * @param int $reviewId
     * @param int $entityPkValue
     *
     * @return void
     */
    public function reAggregateReview($reviewId, $entityPkValue)
    {
        $this->_aggregateRatings($this->_loadVotedRatingIds($reviewId), $entityPkValue);
    }

    /**
     * Get review entity type id by code
     *
     * @param string $entityCode
     *
     * @return int|bool
     */
    public function getEntityIdByCode($entityCode)
    {
        $connection = $this->getConnection();
        $select     = $connection->select()->from($this->reviewEntityTable, ['entity_id'])
            ->where('entity_code = :entity_code');

        return $connection->fetchOne($select, [':entity_code' => $entityCode]);
    }

    /**
     * Delete reviews by product id.
     * Better to call this method in transaction, because operation performed on two separated tables
     *
     * @param int $productId
     *
     * @return $this
     */
    public function deleteReviewsByProductId($productId)
    {
        $this->getConnection()->delete(
            $this->_reviewTable,
            [
                'entity_pk_value=?' => $productId,
                'entity_id=?'       => $this->getEntityIdByCode(\Magento\Review\Model\Review::ENTITY_PRODUCT_CODE),
            ]
        );
        $this->getConnection()->delete(
            $this->getTable('review_entity_summary'),
            [
                'entity_pk_value=?' => $productId,
                'entity_type=?'     => $this->getEntityIdByCode(\Magento\Review\Model\Review::ENTITY_PRODUCT_CODE),
            ]
        );

        return $this;
    }

    public function loadRatingVotes(ReviewInterface $review): void
    {
        $votesCollection = $this->voteFactory->create()->getResourceCollection()->setReviewFilter(
            $review->getId()
        )->setStoreFilter(
            $this->storeManager->getStore()->getId()
        )->addRatingInfo(
            $this->storeManager->getStore()->getId()
        )->load();
        $review->setRatingVotes($votesCollection);
    }
}
