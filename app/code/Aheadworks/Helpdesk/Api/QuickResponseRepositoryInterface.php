<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Helpdesk\Api;

/**
 * Quick response CRUD interface
 * @api
 */
interface QuickResponseRepositoryInterface
{
    /**
     * Save quick response
     *
     * @param \Aheadworks\Helpdesk\Api\Data\QuickResponseInterface $quickResponse
     * @return \Aheadworks\Helpdesk\Api\Data\QuickResponseInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(\Aheadworks\Helpdesk\Api\Data\QuickResponseInterface $quickResponse);

    /**
     * Retrieve quick response by id
     *
     * @param int $quickResponseId
     * @param int|null $storeId
     * @return \Aheadworks\Helpdesk\Api\Data\QuickResponseInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($quickResponseId, $storeId = null);

    /**
     * Retrieve quick response matching the specified criteria
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @param int|null $storeId
     * @return \Aheadworks\Helpdesk\Api\Data\QuickResponseSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria, $storeId = null);

    /**
     * Delete quick response
     *
     * @param \Aheadworks\Helpdesk\Api\Data\QuickResponseInterface $quickResponse
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(\Aheadworks\Helpdesk\Api\Data\QuickResponseInterface $quickResponse);

    /**
     * Delete quick response by ID
     *
     * @param int $quickResponseId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($quickResponseId);
}
