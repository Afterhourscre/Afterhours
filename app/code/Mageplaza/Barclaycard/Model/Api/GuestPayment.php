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

namespace Mageplaza\Barclaycard\Model\Api;

use Magento\Quote\Model\QuoteIdMask;
use Magento\Quote\Model\QuoteIdMaskFactory;
use Mageplaza\Barclaycard\Api\GuestPaymentInterface;
use Mageplaza\Barclaycard\Api\PaymentInterface;

/**
 * Class GuestPayment
 * @package Mageplaza\Barclaycard\Model
 */
class GuestPayment implements GuestPaymentInterface
{
    /**
     * @var QuoteIdMaskFactory
     */
    private $quoteIdMaskFactory;

    /**
     * @var PaymentInterface
     */
    private $payment;

    /**
     * GuestPayment constructor.
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
    public function getHostedUrl($cartId)
    {
        /** @var QuoteIdMask $quoteIdMask */
        $quoteIdMask = $this->quoteIdMaskFactory->create()->load($cartId, 'masked_id');

        return $this->payment->getHostedUrl($quoteIdMask->getQuoteId());
    }

    /**
     * {@inheritDoc}
     */
    public function process3DS($cartId)
    {
        /** @var QuoteIdMask $quoteIdMask */
        $quoteIdMask = $this->quoteIdMaskFactory->create()->load($cartId, 'masked_id');

        return $this->payment->process3DS($quoteIdMask->getQuoteId());
    }
}
