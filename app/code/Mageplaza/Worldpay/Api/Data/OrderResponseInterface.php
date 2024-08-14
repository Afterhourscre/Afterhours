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

namespace Mageplaza\Worldpay\Api\Data;

/**
 * Interface OrderResponseInterface
 * @package Mageplaza\Worldpay\Api\Data
 */
interface OrderResponseInterface
{
    /**
     * Constants defined for keys of array, makes typos less likely
     */
    const REDIRECT_URL = 'redirectUrl';
    const PA_REQ       = 'paReq';
    const MESSAGE      = 'message';

    /**
     * @return string
     */
    public function getRedirectUrl();

    /**
     * @param $value
     *
     * @return $this
     */
    public function setRedirectUrl($value);

    /**
     * @return string
     */
    public function getPaReq();

    /**
     * @param $value
     *
     * @return $this
     */
    public function setPaReq($value);

    /**
     * @return string
     */
    public function getMessage();

    /**
     * @param $value
     *
     * @return $this
     */
    public function setMessage($value);
}
