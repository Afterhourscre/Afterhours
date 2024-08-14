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
 * Class RecentRequested
 * @package Mageplaza\CallForPrice\Block\Report
 */
class RecentRequested extends AbstractReport
{
    const NAME              = 'recentRequestedProducts';
    const MAGE_REPORT_CLASS = \Mageplaza\CallForPrice\Block\Adminhtml\Requests\RecentRequestTimeRange::class;

    /**
     * @return string
     */
    public function getTitle()
    {
        return __('Call for price: Requests');
    }

    /**
     * @return bool
     */
    public function canShowDetail()
    {
        return true;
    }

    /**
     * @return string
     */
    public function getDetailUrl()
    {
        $dateRange = $this->_helperData->getDateRange();
        $store     = $this->getRequest()->getParam('store');

        return $this->getUrl('mpcallforprice/requests/index', [
            'startDate' => $dateRange[0],
            'endDate'   => $dateRange[1],
            'store'     => $store
        ]);
    }
}