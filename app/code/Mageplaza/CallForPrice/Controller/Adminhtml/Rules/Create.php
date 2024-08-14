<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_CallForPrice
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\CallForPrice\Controller\Adminhtml\Rules;

use Mageplaza\CallForPrice\Controller\Adminhtml\Rules;

/**
 * Class Create
 * @package Mageplaza\CallForPrice\Controller\Adminhtml\Rules
 */
class Create extends Rules
{
    /**
     * Create new rule
     *
     * @return void
     */
    public function execute()
    {
        $this->_forward('edit');
    }
}
