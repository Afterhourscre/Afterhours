<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the commercial license
 * that is bundled with this package in the file LICENSE.txt.
 *
 * @category Extait
 * @package Extait_Cookie
 * @copyright Copyright (c) 2016-2018 Extait, Inc. (http://www.extait.com)
 */

namespace Extait\Cookie\Api\Data;

use Magento\Framework\Api\SearchCriteriaInterface;

/** @api */
interface CategoryRepositoryInterface
{
    /**
     * Get the cookie category by ID.
     *
     * @param int $id
     * @param null|int $storeID
     * @return \Extait\Cookie\Api\Data\CategoryInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($id, $storeID = null);

    /**
     * Save the cookie category entity.
     *
     * @param \Extait\Cookie\Api\Data\CategoryInterface $category
     * @return \Extait\Cookie\Api\Data\CategoryInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    public function save(CategoryInterface $category);

    /**
     * Delete the cookie category entity.
     *
     * @param \Extait\Cookie\Api\Data\CategoryInterface|\Magento\Framework\Model\AbstractModel $category
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(CategoryInterface $category);

    /**
     * Get list of cookie categories according to the search criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @param null|int $storeID
     * @return \Extait\Cookie\Api\Data\CategorySearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria, $storeID = null);
}
