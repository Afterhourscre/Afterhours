<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the mageplaza.com license that is
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

namespace Mageplaza\CallForPrice\Block\Report;

/**
 * Class TopRequested
 * @package Mageplaza\CallForPrice\Block\Report
 */
class TopRequested extends AbstractReport
{
    const NAME              = 'topRequestedProducts';
    const MAGE_REPORT_CLASS = \Mageplaza\CallForPrice\Block\Adminhtml\Requests\TopRequestedProducts::class;

    /**
     * @return string
     */
    public function getTitle()
    {
        return __('Top Requested Product');
    }
}