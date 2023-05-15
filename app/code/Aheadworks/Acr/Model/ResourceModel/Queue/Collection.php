<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Acr\Model\ResourceModel\Queue;

use Aheadworks\Acr\Model\Queue;
use Aheadworks\Acr\Model\ResourceModel\Queue as QueueResource;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class Collection
 * @package Aheadworks\Acr\Model\ResourceModel\Queue
 */
class Collection extends AbstractCollection
{
    /**
     * {@inheritdoc}
     */
    protected $_idFieldName = 'id';

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(Queue::class, QueueResource::class);
    }

    /**
     * Filter collection by status
     *
     * @param int $status
     * @return $this
     */
    public function filterByStatus($status)
    {
        if (is_integer($status)) {
            $this->addFieldToFilter('status', ['eq' => $status]);
        }
        return $this;
    }

    /**
     * Join with rule table
     *
     * @return $this
     */
    public function joinRule()
    {
        $this->getSelect()
            ->join(
                ['rule' => $this->getTable('aw_acr_rule')],
                'main_table.rule_id = rule.id',
                ['rule_name' => 'rule.name']
            )
        ;
        return $this;
    }

    /**
     * Join with cart history table
     *
     * @return $this
     */
    public function joinCartHistory()
    {
        $this->getSelect()
            ->join(
                ['cart_history' => $this->getTable('aw_acr_cart_history')],
                'main_table.cart_history_id = cart_history.id',
                []
            )
        ;
        return $this;
    }
}
