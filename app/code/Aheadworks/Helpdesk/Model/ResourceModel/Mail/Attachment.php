<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Helpdesk\Model\ResourceModel\Mail;

/**
 * Class Attachment
 * @package Aheadworks\Helpdesk\Model\ResourceModel\Mail
 */
class Attachment extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('aw_helpdesk_gateway_mail_attachment', 'id');
    }
}
