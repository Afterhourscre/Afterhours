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

namespace Mageplaza\CallForPrice\Block\Adminhtml;

use Magento\Backend\Block\Template;
use Mageplaza\CallForPrice\Helper\Data;

/**
 * Class Menu
 * @package Mageplaza\CallForPrice\Block\Adminhtml
 */
class Menu extends Template
{
    /**
     * @var Data
     */
    protected $helperData;

    /**
     * Menu constructor.
     *
     * @param Template\Context $context
     * @param Data $helperData
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Data $helperData,
        array $data = [])
    {
        parent::__construct($context, $data);

        $this->helperData = $helperData;
    }

    /**
     * @return array
     */
    public function getDateRange()
    {
        $dateRange = $this->helperData->getDateRange();
        if ($this->getRequest()->getParam('startDate') && $this->getRequest()->getParam('endDate')) {
            $dateRange[0] = $this->getRequest()->getParam('startDate');
            $dateRange[1] = $this->getRequest()->getParam('endDate');

            return $dateRange;
        }
        /** if date range is null*/
        if (!$dateRange) {
            list($startDate, $endDate) = $this->helperData->getDateTimeRangeFormat('now');
            $dateRange[0] = $startDate;
            $dateRange[1] = $endDate;
        }

        return $dateRange;
    }

    /**
     * @return bool
     */
    public function isAllowUse()
    {
        $actionName = $this->getRequest()->getActionName();

        return $actionName == 'index';
    }
}