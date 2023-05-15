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
 * @package     Mageplaza_Barclaycard
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\Barclaycard\Controller\Payment;

use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Model\Quote;
use Mageplaza\Barclaycard\Controller\PlaceOrder;

/**
 * Class Cancel
 * @package Mageplaza\Barclaycard\Controller\Payment
 */
class Cancel extends PlaceOrder
{
    /**
     * @param Quote $quote
     *
     * @throws LocalizedException
     */
    protected function paymentHandler($quote)
    {
        throw new LocalizedException(__('The payment is cancelled.'));
    }
}
