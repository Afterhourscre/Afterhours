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
 * @package     Mageplaza_Worldpay
 * @copyright   Copyright (c) Mageplaza (http://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\Worldpay\Gateway\Config;

use Mageplaza\Worldpay\Model\Source\PaymentAction;

/**
 * Class Config
 * @package Mageplaza\Worldpay\Gateway\Config
 */
abstract class Config extends \Magento\Payment\Gateway\Config\Config
{
    /**
     * @return bool
     */
    public function isActive()
    {
        return (bool) $this->getValue('active');
    }

    /**
     * @return string
     */
    public function getPaymentAction()
    {
        return $this->getValue('payment_action');
    }

    /**
     * @return array
     */
    public function getSpecificCountry()
    {
        $result = $this->getValue('specificcountry');

        return $result ? explode(',', $result) : [];
    }

    /**
     * @return string
     */
    public function getOrderStatus()
    {
        return $this->getValue('order_status');
    }

    /**
     * @return bool
     */
    public function isAuthorize()
    {
        return $this->getPaymentAction() === PaymentAction::ACTION_AUTHORIZE;
    }

    /**
     * @return bool
     */
    public function isUse3ds()
    {
        return (bool) $this->getValue('use3ds');
    }
}
