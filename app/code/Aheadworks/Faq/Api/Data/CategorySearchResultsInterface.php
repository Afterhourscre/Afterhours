<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */


namespace Aheadworks\Faq\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface for FAQ category search results
 *
 * @api
 */
interface CategorySearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get categories list
     *
     * @return \Aheadworks\Faq\Api\Data\CategoryInterface[]
     */
    public function getItems();

    /**
     * Set articles list
     *
     * @param \Aheadworks\Faq\Api\Data\CategoryInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
