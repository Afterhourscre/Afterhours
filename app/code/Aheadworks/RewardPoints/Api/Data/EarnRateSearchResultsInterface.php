<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\RewardPoints\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface EarnRateSearchResultsInterface
 * @package Aheadworks\RewardPoints\Api\Data
 * @api
 */
interface EarnRateSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get ruratele list
     *
     * @return \Aheadworks\RewardPoints\Api\Data\EarnRateInterface[]
     */
    public function getItems();

    /**
     * Set rate list
     *
     * @param \Aheadworks\RewardPoints\Api\Data\EarnRateInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
