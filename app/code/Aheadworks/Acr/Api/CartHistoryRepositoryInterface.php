<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Acr\Api;

use Aheadworks\Acr\Api\Data\CartHistoryInterface;
use Magento\Framework\Api\SearchCriteriaInterface;

/**
 * CartHistory CRUD interface
 * @api
 */
interface CartHistoryRepositoryInterface
{
    /**
     * Save cart history
     *
     * @param \Aheadworks\Acr\Api\Data\CartHistoryInterface $cartHistory
     * @return \Aheadworks\Acr\Api\Data\CartHistoryInterface
     * @throws \Magento\Framework\Exception\LocalizedException If validation fails
     */
    public function save(CartHistoryInterface $cartHistory);

    /**
     * Retrieve cart history
     *
     * @param int $cartHistoryId
     * @return \Aheadworks\Acr\Api\Data\CartHistoryInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException If cart history does not exist
     */
    public function get($cartHistoryId);

    /**
     * Retrieve cart histories matching the specified criteria
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Aheadworks\Acr\Api\Data\CartHistorySearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria);

    /**
     * Delete cart history
     *
     * @param \Aheadworks\Acr\Api\Data\CartHistoryInterface $cartHistory
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException If cart history does not exist
     */
    public function delete(CartHistoryInterface $cartHistory);

    /**
     * Delete cart history by id
     *
     * @param int $cartHistoryId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException If cart history does not exist
     */
    public function deleteById($cartHistoryId);
}
