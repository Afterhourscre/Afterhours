<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Acr\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface CartHistorySearchResultsInterface
 * @package Aheadworks\Acr\Api\Data
 * @api
 */
interface CartHistorySearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get cart history list
     *
     * @return \Aheadworks\Acr\Api\Data\CartHistoryInterface[]
     */
    public function getItems();

    /**
     * Set cart history list
     *
     * @param \Aheadworks\Acr\Api\Data\CartHistoryInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
