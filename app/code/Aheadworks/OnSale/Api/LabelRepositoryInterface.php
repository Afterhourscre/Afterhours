<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Api;

/**
 * Label CRUD interface
 * @api
 */
interface LabelRepositoryInterface
{
    /**
     * Save label
     *
     * @param \Aheadworks\OnSale\Api\Data\LabelInterface $label
     * @return \Aheadworks\OnSale\Api\Data\LabelInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(\Aheadworks\OnSale\Api\Data\LabelInterface $label);

    /**
     * Retrieve label by ID
     *
     * @param int $labelId
     * @return \Aheadworks\OnSale\Api\Data\LabelInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($labelId);

    /**
     * Retrieve labels matching the specified criteria
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Aheadworks\OnSale\Api\Data\LabelSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * Delete label
     *
     * @param \Aheadworks\OnSale\Api\Data\LabelInterface $label
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(\Aheadworks\OnSale\Api\Data\LabelInterface $label);

    /**
     * Delete label by ID
     *
     * @param int $labelId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($labelId);
}
