<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Model\Indexer\Rule\Action;

use Aheadworks\OnSale\Model\Indexer\Rule\AbstractAction;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Full
 *
 * @package Aheadworks\OnSale\Model\Indexer\Rule\Action
 */
class Full extends AbstractAction
{
    /**
     * Execute Full reindex
     *
     * @param array|int|null $ids
     * @return void
     * @throws LocalizedException
     */
    public function execute($ids = null)
    {
        try {
            $this->resourceRuleProductIndexer->reindexAll();
        } catch (\Exception $e) {
            throw new LocalizedException(__($e->getMessage()), $e);
        }
    }
}
