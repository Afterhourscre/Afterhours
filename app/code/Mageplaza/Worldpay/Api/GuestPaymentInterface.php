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

namespace Mageplaza\Worldpay\Api;

/**
 * Interface GuestPaymentInterface
 * @package Mageplaza\Worldpay\Api
 */
interface GuestPaymentInterface
{
    /**
     * @param string $cartId
     *
     * @return \Mageplaza\Worldpay\Api\Data\OrderResponseInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function apply3ds($cartId);

    /**
     * @param string $cartId
     * @param string $token
     *
     * @return \Mageplaza\Worldpay\Api\Data\OrderResponseInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function processApm($cartId, $token);
}
