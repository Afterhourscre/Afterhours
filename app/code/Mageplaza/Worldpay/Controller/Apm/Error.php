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
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\Worldpay\Controller\Apm;

use InvalidArgumentException;
use Magento\Quote\Model\Quote;
use Mageplaza\Worldpay\Controller\PlaceOrder;

/**
 * Class Error
 * @package Mageplaza\Worldpay\Controller\Apm
 */
class Error extends PlaceOrder
{
    /**
     * @param Quote $quote
     */
    public function paymentHandler($quote)
    {
        throw new InvalidArgumentException(__('An error occurred on the server. Please try to place the order again'));
    }
}
