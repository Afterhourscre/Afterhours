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
namespace Mageplaza\Worldpay\Gateway\Http\Client;

use Magento\Payment\Gateway\Http\ClientInterface;
use Magento\Payment\Gateway\Http\TransferInterface;
use Magento\Payment\Model\Method\Logger;
use Mageplaza\Worldpay\Helper\Request;

/**
 * Class ClientMock
 * @package Mageplaza\Worldpay\Gateway\Http\Client
 */
class ClientMock implements ClientInterface
{
    /**
     * @var Request
     */
    private $helper;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * ClientMock constructor.
     *
     * @param Request $helper
     * @param Logger $logger
     */
    public function __construct(
        Request $helper,
        Logger $logger
    ) {
        $this->helper = $helper;
        $this->logger = $logger;
    }

    /**
     * @param TransferInterface $transferObject
     *
     * @return array
     */
    public function placeRequest(TransferInterface $transferObject)
    {
        $txnArray = $transferObject->getBody();
        $response = $this->helper->createTransaction($txnArray);

        $this->logger->debug(['client request' => $txnArray, 'client response' => $response]);

        return $response;
    }
}
