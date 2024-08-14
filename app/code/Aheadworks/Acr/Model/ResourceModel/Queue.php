<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Acr\Model\ResourceModel;

use Aheadworks\Acr\Api\Data\QueueInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class Queue
 * @package Aheadworks\Acr\Model\ResourceModel
 */
class Queue extends AbstractDb
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init('aw_acr_queue', 'id');
    }

    /**
     * Delete queue items by cart history id
     *
     * @param int $cartHistoryId
     * @return $this
     */
    public function deleteItemsByCartHistory($cartHistoryId)
    {
        $writeAdapter = $this->getConnection();
        $conditions = sprintf(
            'cart_history_id=%s AND status<>%s',
            $writeAdapter->quote($cartHistoryId),
            $writeAdapter->quote(QueueInterface::STATUS_SENT)
        );
        $writeAdapter->delete($this->getMainTable(), $conditions);

        return $this;
    }
}
