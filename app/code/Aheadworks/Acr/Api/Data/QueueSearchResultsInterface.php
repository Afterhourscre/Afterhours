<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Acr\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface QueueSearchResultsInterface
 * @package Aheadworks\Acr\Api\Data
 * @api
 */
interface QueueSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get queue list
     *
     * @return \Aheadworks\Acr\Api\Data\QueueInterface[]
     */
    public function getItems();

    /**
     * Set queue list
     *
     * @param \Aheadworks\Acr\Api\Data\QueueInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
