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
interface CookieRepositoryInterface
{
    /**
     * Get the cookie by ID.
     *
     * @param int $id
     * @param null|int $storeID
     * @return \Extait\Cookie\Api\Data\CookieInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($id, $storeID = null);

    /**
     * Get the cookie by name.
     *
     * @param string $name
     * @return \Extait\Cookie\Api\Data\CookieInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getByName($name);

    /**
     * Save the cookie entity.
     *
     * @param \Extait\Cookie\Api\Data\CookieInterface $cookie
     * @return \Extait\Cookie\Api\Data\CookieInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    public function save(CookieInterface $cookie);

    /**
     * Delete the cookie entity.
     *
     * @param \Extait\Cookie\Api\Data\CookieInterface|\Magento\Framework\Model\AbstractModel $cookie
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(CookieInterface $cookie);

    /**
     * Get list of cookies according to the search criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @param null|int $storeID
     * @return \Extait\Cookie\Api\Data\CookieSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria, $storeID = null);
}
