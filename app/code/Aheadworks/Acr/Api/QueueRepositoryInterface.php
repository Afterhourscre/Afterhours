<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Acr\Api;

use Aheadworks\Acr\Api\Data\QueueInterface;
use Magento\Framework\Api\SearchCriteriaInterface;

/**
 * Queue CRUD interface
 * @api
 */
interface QueueRepositoryInterface
{
    /**
     * Save queue
     *
     * @param \Aheadworks\Acr\Api\Data\QueueInterface $queue
     * @return \Aheadworks\Acr\Api\Data\QueueInterface
     * @throws \Magento\Framework\Exception\LocalizedException If validation fails
     */
    public function save(QueueInterface $queue);

    /**
     * Retrieve queue
     *
     * @param int $queueId
     * @return \Aheadworks\Acr\Api\Data\QueueInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException If queue does not exist
     */
    public function get($queueId);

    /**
     * Retrieve queues matching the specified criteria
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Aheadworks\Acr\Api\Data\QueueSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria);

    /**
     * Delete queue
     * Related cart history item will be deleted if there are no more queue items with this cart history id
     *
     * @param \Aheadworks\Acr\Api\Data\QueueInterface $queue
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException If queue does not exist
     */
    public function delete(QueueInterface $queue);

    /**
     * Delete queue by id
     * Related cart history item will be deleted if there are no more queue items with this cart history id
     *
     * @param int $queueId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException If queue does not exist
     */
    public function deleteById($queueId);

    /**
     * Delete queue by cart history id
     * Related cart history item will NOT be deleted!!!
     *
     * @param int $cartHistoryId
     * @return bool true on success
     */
    public function deleteByCartHistoryId($cartHistoryId);
}
