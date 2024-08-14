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

namespace Mageplaza\Worldpay\Gateway\Response;

use DateInterval;
use DateTime;
use DateTimeZone;
use Exception;
use Magento\Payment\Gateway\Response\HandlerInterface;
use Magento\Payment\Model\InfoInterface;
use Magento\Sales\Api\Data\OrderPaymentExtensionInterface;
use Magento\Sales\Api\Data\OrderPaymentExtensionInterfaceFactory;
use Magento\Sales\Model\Order\Payment;
use Magento\Vault\Api\Data\PaymentTokenInterface;
use Magento\Vault\Api\Data\PaymentTokenInterfaceFactory;
use Magento\Vault\Model\Ui\VaultConfigProvider;
use Mageplaza\Worldpay\Helper\Response;
use Mageplaza\Worldpay\Model\Source\CardTypeMapper;

/**
 * Class VaultDetailsHandler
 * @package Mageplaza\Worldpay\Gateway\Response
 */
class VaultDetailsHandler extends AbstractResponseHandler implements HandlerInterface
{
    /**
     * @var PaymentTokenInterfaceFactory
     */
    private $paymentTokenFactory;

    /**
     * @var OrderPaymentExtensionInterfaceFactory
     */
    private $paymentExtensionFactory;

    /**
     * VaultDetailsHandler constructor.
     *
     * @param Response $helper
     * @param PaymentTokenInterfaceFactory $paymentTokenFactory
     * @param OrderPaymentExtensionInterfaceFactory $paymentExtensionFactory
     */
    public function __construct(
        Response $helper,
        PaymentTokenInterfaceFactory $paymentTokenFactory,
        OrderPaymentExtensionInterfaceFactory $paymentExtensionFactory
    ) {
        $this->paymentTokenFactory     = $paymentTokenFactory;
        $this->paymentExtensionFactory = $paymentExtensionFactory;

        parent::__construct($helper);
    }

    /**
     * @param array $handlingSubject
     * @param array $response
     *
     * @throws Exception
     */
    public function handle(array $handlingSubject, array $response)
    {
        $payment = $this->getValidPaymentInstance($handlingSubject);

        if (!$payment->getAdditionalInformation(VaultConfigProvider::IS_ACTIVE_CODE)) {
            return;
        }

        // add vault payment token entity to extension attributes
        if ($paymentToken = $this->getVaultPaymentToken($response)) {
            $extensionAttributes = $this->getExtensionAttributes($payment);
            $extensionAttributes->setVaultPaymentToken($paymentToken);
        }
    }

    /**
     * Get vault payment token entity
     *
     * @param array $response
     *
     * @return PaymentTokenInterface|null
     * @throws Exception
     */
    protected function getVaultPaymentToken($response)
    {
        if (!$card = $this->helper->getInfo($response, 'paymentResponse')) {
            return null;
        }

        $expMonth = $this->helper->getInfo($card, 'expiryMonth');
        $expYear  = $this->helper->getInfo($card, 'expiryYear');

        $details = [
            'type'           => CardTypeMapper::getCardType($this->helper->getInfo($card, 'cardType')),
            'maskedCC'       => substr($this->helper->getInfo($card, 'maskedCardNumber'), -4, 4),
            'expirationDate' => $expMonth . '/' . $expYear
        ];

        /** @var PaymentTokenInterface $paymentToken */
        $paymentToken = $this->paymentTokenFactory->create();
        $paymentToken->setGatewayToken($this->helper->getInfo($response, 'token'));
        $paymentToken->setExpiresAt($this->getExpirationDate($expMonth, $expYear));
        $paymentToken->setTokenDetails(Response::jsonEncode($details));

        return $paymentToken;
    }

    /**
     * @param string $expMonth
     * @param string $expYear
     *
     * @return string
     * @throws Exception
     */
    private function getExpirationDate($expMonth, $expYear)
    {
        $expDate = new DateTime(
            $expYear
            . '-'
            . $expMonth
            . '-'
            . '01'
            . ' '
            . '00:00:00',
            new DateTimeZone('UTC')
        );
        $expDate->add(new DateInterval('P1M'));

        return $expDate->format('Y-m-d 00:00:00');
    }

    /**
     * @param InfoInterface|Payment $payment
     *
     * @return OrderPaymentExtensionInterface
     */
    private function getExtensionAttributes(InfoInterface $payment)
    {
        $extensionAttributes = $payment->getExtensionAttributes();

        if ($extensionAttributes === null) {
            $extensionAttributes = $this->paymentExtensionFactory->create();
            $payment->setExtensionAttributes($extensionAttributes);
        }

        return $extensionAttributes;
    }
}
