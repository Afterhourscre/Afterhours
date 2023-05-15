<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Plugin\Indexer\Stock;

use Aheadworks\OnSale\Model\Indexer\Rule\Processor as RuleProcessor;
use Magento\CatalogInventory\Model\Indexer\Stock\Action\Rows as RowsAction;

/**
 * Class UpdateRowsPlugin
 *
 * @package Aheadworks\OnSale\Plugin\Indexer\Stock
 */
class UpdateRowsPlugin
{
    /**
     * @var RuleProcessor
     */
    protected $ruleProcessor;

    /**
     * @param RuleProcessor $ruleProcessor
     */
    public function __construct(RuleProcessor $ruleProcessor)
    {
        $this->ruleProcessor = $ruleProcessor;
    }

    /**
     * Reindex on sale rule index after update of catalog inventory index
     *
     * @param RowsAction $subject
     * @param \Closure $proceed
     * @param $ids
     */
    public function aroundExecute(
        $subject,
        \Closure $proceed,
        $ids
    ) {
        $proceed($ids);
        $this->ruleProcessor->reindexList($ids);
    }
}
