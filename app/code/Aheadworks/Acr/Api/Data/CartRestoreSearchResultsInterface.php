<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Acr\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface CartRestoreSearchResultsInterface
 * @package Aheadworks\Acr\Api\Data
 * @api
 */
interface CartRestoreSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get cart restore list
     *
     * @return \Aheadworks\Acr\Api\Data\CartRestoreInterface[]
     */
    public function getItems();

    /**
     * Set cart restore list
     *
     * @param \Aheadworks\Acr\Api\Data\CartRestoreInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
