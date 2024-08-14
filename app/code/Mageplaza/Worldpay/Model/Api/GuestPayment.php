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

namespace Mageplaza\Worldpay\Model\Api;

use Magento\Quote\Model\QuoteIdMask;
use Magento\Quote\Model\QuoteIdMaskFactory;
use Mageplaza\Worldpay\Api\GuestPaymentInterface;
use Mageplaza\Worldpay\Api\PaymentInterface;

/**
 * Class GuestPayment
 * @package Mageplaza\Worldpay\Model
 */
class GuestPayment implements GuestPaymentInterface
{
    /**
     * @var QuoteIdMaskFactory
     */
    protected $quoteIdMaskFactory;

    /**
     * @var PaymentInterface
     */
    protected $payment;

    /**
     * GuestCheckoutManagement constructor.
     *
     * @param QuoteIdMaskFactory $quoteIdMaskFactory
     * @param PaymentInterface $payment
     */
    public function __construct(
        QuoteIdMaskFactory $quoteIdMaskFactory,
        PaymentInterface $payment
    ) {
        $this->quoteIdMaskFactory = $quoteIdMaskFactory;
        $this->payment            = $payment;
    }

    /**
     * {@inheritDoc}
     */
    public function apply3ds($cartId)
    {
        /** @var QuoteIdMask $quoteIdMask */
        $quoteIdMask = $this->quoteIdMaskFactory->create()->load($cartId, 'masked_id');

        return $this->payment->apply3ds($quoteIdMask->getQuoteId());
    }

    /**
     * {@inheritDoc}
     */
    public function processApm($cartId, $token)
    {
        /** @var QuoteIdMask $quoteIdMask */
        $quoteIdMask = $this->quoteIdMaskFactory->create()->load($cartId, 'masked_id');

        return $this->payment->processApm($quoteIdMask->getQuoteId(), $token);
    }
}
