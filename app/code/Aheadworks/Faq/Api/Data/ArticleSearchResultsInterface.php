<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */


namespace Aheadworks\Faq\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface for FAQ article search results
 *
 * @api
 */
interface ArticleSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get articles list
     *
     * @return \Aheadworks\Faq\Api\Data\ArticleInterface[]
     */
    public function getItems();

    /**
     * Set articles list
     *
     * @param \Aheadworks\Faq\Api\Data\ArticleInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
