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

namespace Mageplaza\CallForPrice\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Mageplaza\CallForPrice\Helper\Data as HelperData;

/**
 * Class ReportInitCards
 * @package Mageplaza\CallForPrice\Observer
 */
class ReportInitCards implements ObserverInterface
{
    /**
     * @var HelperData
     */
    private $helperData;

    /**
     * ReportInitCards constructor.
     * @param HelperData $helperData
     */
    public function __construct(HelperData $helperData)
    {
        $this->helperData = $helperData;
    }

    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        if ($this->helperData->isEnabled()) {
            $cards = $observer->getCards();
            $cards->addData(['topRequestedProducts' => 'Mageplaza\CallForPrice\Block\Report\TopRequested']);
            $cards->addData(['recentRequestedProducts' => 'Mageplaza\CallForPrice\Block\Report\RecentRequested']);
        }
    }
}
