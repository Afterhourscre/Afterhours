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

namespace Mageplaza\Barclaycard\Api;

use Exception;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Interface PaymentInterface
 * @package Mageplaza\Barclaycard\Api
 */
interface PaymentInterface
{
    /**
     * @param string $cartId
     *
     * @return string
     * @throws NoSuchEntityException
     * @throws LocalizedException
     * @throws Exception
     */
    public function getHostedUrl($cartId);

    /**
     * @param string $cartId
     *
     * @return string
     * @throws NoSuchEntityException
     * @throws LocalizedException
     * @throws Exception
     */
    public function process3DS($cartId);
}
