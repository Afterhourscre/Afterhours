<?php


namespace Webkul\MagentoChatSystem\Api\Data;

interface ReportSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get Report list.
     * @return \Webkul\MagentoChatSystem\Api\Data\ReportInterface[]
     */
    public function getItems();

    /**
     * Set customer_id list.
     * @param \Webkul\MagentoChatSystem\Api\Data\ReportInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
