<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Helpdesk\Model\ResourceModel\Attachment;

/**
 * Class Collection
 * @package Aheadworks\Helpdesk\Model\ResourceModel\Attachment
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \Aheadworks\Helpdesk\Model\Attachment::class,
            \Aheadworks\Helpdesk\Model\ResourceModel\Attachment::class
        );
    }
}
