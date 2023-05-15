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
use Mageplaza\CallForPrice\Helper\Rule as HelperRule;
use Mageplaza\CallForPrice\Model\Status;

/**
 * Class CallForPriceLayoutLoadBefore
 * @package Mageplaza\CallForPrice\Observer
 */
class CallForPriceLayoutLoadBefore implements ObserverInterface
{
    /**
     * @var HelperData
     */
    private $helperData;

    /**
     * @var HelperRule
     */
    private $helperRule;

    /**
     * CallForPriceLayoutLoadBefore constructor.
     * @param HelperData $helperData
     * @param HelperRule $helperRule
     */
    public function __construct(
        HelperData $helperData,
        HelperRule $helperRule
    )
    {
        $this->helperData = $helperData;
        $this->helperRule = $helperRule;
    }

    /**
     * @param Observer $observer
     *
     * @return void
     */
    public function execute(Observer $observer)
    {
        if ($this->helperData->isEnabled() && $this->helperData->getDisableCompareWishlistConfig()) {
            /** get layout loading*/
            $layout = $observer->getData('layout');
            /** add handle layout custom to disable default function*/
            $layout->getUpdate()->addHandle('disable_compare_wishlist_product');
        }

        if ($this->helperData->isEnabled()) {
            $ruleCollection = $this->helperRule->getRulesCollection();
            foreach ($ruleCollection as $rule) {
                $checkCustomerGroup = $this->helperRule->checkCustomerGroup($rule->getRuleId());
                $expireDate         = $this->helperRule->checkExpireDate($rule->getRuleId());
                if ($checkCustomerGroup && $expireDate && $rule->getStatus() == Status::ENABLED) {
                    /** hide recently ordered*/
                    $layout = $observer->getData('layout');
                    $layout->getUpdate()->addHandle('hiden_recently_ordered');
                }
            }
        }
    }
}