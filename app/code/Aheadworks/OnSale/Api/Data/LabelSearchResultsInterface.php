<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface LabelSearchResultsInterface
 * @api
 */
interface LabelSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get label list
     *
     * @return \Aheadworks\OnSale\Api\Data\LabelInterface[]
     */
    public function getItems();

    /**
     * Set label list
     *
     * @param \Aheadworks\OnSale\Api\Data\LabelInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
