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

namespace Mageplaza\Barclaycard\Gateway\Response;

use Magento\Framework\Exception\LocalizedException;
use Magento\Payment\Gateway\Response\HandlerInterface;

/**
 * Class CaptureResponseHandler
 * @package Mageplaza\Barclaycard\Gateway\Response
 */
class CaptureResponseHandler extends AbstractResponseHandler implements HandlerInterface
{
    /**
     * @param array $handlingSubject
     * @param array $response
     *
     * @throws LocalizedException
     */
    public function handle(array $handlingSubject, array $response)
    {
        $this->handleResponse($handlingSubject, $response, true);
    }
}
