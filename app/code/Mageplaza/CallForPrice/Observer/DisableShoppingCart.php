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
use Magento\Framework\UrlInterface;
use Mageplaza\CallForPrice\Helper\Data as HelperData;

/**
 * Class DisableShoppingCart
 * @package Mageplaza\CallForPrice\Observer
 */
class DisableShoppingCart implements ObserverInterface
{
    /**
     * @var HelperData
     */
    private $helperData;

    /**
     * @var UrlInterface
     */
    protected $url;

    /**
     * DisableShoppingCart constructor.
     *
     * @param HelperData $helperData
     * @param UrlInterface $url
     */
    public function __construct(
        HelperData $helperData,
        UrlInterface $url
    )
    {
        $this->helperData = $helperData;
        $this->url        = $url;
    }

    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $groupDisabled   = $this->helperData->getDisableCartByGroupConfig();
        $customerGroupId = $this->helperData->getCustomerGroupId();

        if ($this->helperData->isEnabled() && in_array($customerGroupId, $groupDisabled)) {
            /**redirect forward to no router*/
            $redirectionUrl = $this->url->getUrl('checkout/noroute');
            $observer->getControllerAction()->getResponse()->setRedirect($redirectionUrl);
        }
    }
}