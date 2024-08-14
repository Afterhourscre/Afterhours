<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */


namespace Aheadworks\OnSale\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface for rule search results
 * @api
 */
interface RuleSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get rule list
     *
     * @return \Aheadworks\OnSale\Api\Data\RuleInterface[]
     */
    public function getItems();

    /**
     * Set rule list
     *
     * @param \Aheadworks\OnSale\Api\Data\RuleInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
