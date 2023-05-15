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

use Magento\Framework\Api\SearchResultsInterface;

/** @api */
interface CategorySearchResultsInterface extends SearchResultsInterface
{
    /**
     * @return \Extait\Cookie\Api\Data\CategoryInterface[]
     */
    public function getItems();

    /**
     * @param \Extait\Cookie\Api\Data\CategoryInterface[] $items
     * @return void
     */
    public function setItems(array $items);
}
