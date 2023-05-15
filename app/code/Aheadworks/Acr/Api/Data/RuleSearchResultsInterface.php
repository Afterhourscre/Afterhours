<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Acr\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface RuleSearchResultsInterface
 * @package Aheadworks\Acr\Api\Data
 * @api
 */
interface RuleSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get rules list
     *
     * @return \Aheadworks\Acr\Api\Data\RuleInterface[]
     */
    public function getItems();

    /**
     * Set rules list
     *
     * @param \Aheadworks\Acr\Api\Data\RuleInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
