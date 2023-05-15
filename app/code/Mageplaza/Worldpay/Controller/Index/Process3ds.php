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

namespace Mageplaza\Worldpay\Controller\Index;

use InvalidArgumentException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Model\Quote;
use Mageplaza\Worldpay\Controller\PlaceOrder;
use Worldpay;

/**
 * Class Process3ds
 * @package Mageplaza\Worldpay\Controller\Index
 */
class Process3ds extends PlaceOrder
{
    /**
     * @param Quote $quote
     *
     * @throws LocalizedException
     */
    public function paymentHandler($quote)
    {
        $worldpay = new Worldpay\Worldpay($this->requestHelper->getServiceKey());
        Worldpay\Utils::setThreeDSShopperObject([
            'shopperIpAddress'    => $this->requestHelper->getIpAddress(),
            'shopperSessionId'    => $this->checkoutSession->getSessionId(),
            'shopperUserAgent'    => $this->requestHelper->getUserAgent(),
            'shopperAcceptHeader' => '*/*'
        ]);

        $hostedRes = $quote->getPayment()->getAdditionalInformation('hostedResponse');
        $orderCode = $this->requestHelper->getInfo($hostedRes, 'orderCode');
        $paRes     = $this->getRequest()->getParam('PaRes');
        $request   = compact('orderCode', 'paRes');

        /** @var array|string $response */
        $response = $worldpay->authorize3DSOrder($orderCode, $paRes);

        $this->paymentLogger->debug(['process 3ds request' => $request, 'process 3ds response' => $response]);

        if ($error = $this->responseHelper->hasError($response)) {
            throw new InvalidArgumentException(__($error));
        }

        $quote->getPayment()->setAdditionalInformation('hostedResponse', $response);
    }
}
