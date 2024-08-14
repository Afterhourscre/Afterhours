<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Helpdesk\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface for quick response search results
 * @api
 */
interface QuickResponseSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get quick response list
     *
     * @return \Aheadworks\Helpdesk\Api\Data\QuickResponseInterface[]
     */
    public function getItems();

    /**
     * Set quick response list
     *
     * @param \Aheadworks\Helpdesk\Api\Data\QuickResponseInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
