<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */


namespace Aheadworks\Faq\Block\Adminhtml\Page;

use \Magento\Backend\Block\Template;

/**
 * FAQ Page Menu
 *
 * @method Menu setTitle(string $title)
 * @method string getTitle()
 */
class Menu extends Template
{
    /**
     * Block template filename
     *
     * @var string
     */
    protected $_template = 'Aheadworks_Faq::page/menu.phtml';
}
