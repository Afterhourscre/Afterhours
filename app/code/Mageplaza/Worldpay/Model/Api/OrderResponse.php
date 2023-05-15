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

use Magento\Framework\Model\AbstractExtensibleModel;
use Mageplaza\Worldpay\Api\Data\OrderResponseInterface;

/**
 * Class OrderResponse
 * @package Mageplaza\Worldpay\Model\Api
 */
class OrderResponse extends AbstractExtensibleModel implements OrderResponseInterface
{
    /**
     * @return string
     */
    public function getRedirectUrl()
    {
        return $this->getData(self::REDIRECT_URL);
    }

    /**
     * @param $value
     *
     * @return $this
     */
    public function setRedirectUrl($value)
    {
        return $this->setData(self::REDIRECT_URL, $value);
    }

    /**
     * @return string
     */
    public function getPaReq()
    {
        return $this->getData(self::PA_REQ);
    }

    /**
     * @param $value
     *
     * @return $this
     */
    public function setPaReq($value)
    {
        return $this->setData(self::PA_REQ, $value);
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->getData(self::MESSAGE);
    }

    /**
     * @param $value
     *
     * @return $this
     */
    public function setMessage($value)
    {
        return $this->setData(self::MESSAGE, $value);
    }
}
