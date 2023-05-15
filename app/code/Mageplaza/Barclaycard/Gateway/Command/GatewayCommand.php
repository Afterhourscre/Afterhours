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
 * @copyright   Copyright (c) Mageplaza (http://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\Barclaycard\Gateway\Command;

use Exception;
use Magento\Payment\Gateway\Command\CommandException;
use Magento\Payment\Gateway\CommandInterface;
use Magento\Payment\Gateway\Http\ClientException;
use Magento\Payment\Gateway\Http\ClientInterface;
use Magento\Payment\Gateway\Http\ConverterException;
use Magento\Payment\Gateway\Http\TransferFactoryInterface;
use Magento\Payment\Gateway\Request\BuilderInterface;
use Magento\Payment\Gateway\Response\HandlerInterface;
use Magento\Payment\Gateway\Validator\ValidatorInterface;
use Magento\Payment\Model\Method\Logger;
use Mageplaza\Barclaycard\Helper\Data;
use Psr\Log\LoggerInterface;

/**
 * Class GatewayCommand
 * @package Mageplaza\Barclaycard\Gateway\Command
 */
class GatewayCommand implements CommandInterface
{
    /**
     * @var BuilderInterface
     */
    private $requestBuilder;

    /**
     * @var TransferFactoryInterface
     */
    private $transferFactory;

    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var HandlerInterface
     */
    private $handler;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @var Logger
     */
    private $paymentLogger;

    /**
     * @var Data
     */
    private $helper;

    /**
     * GatewayCommand constructor.
     *
     * @param BuilderInterface $requestBuilder
     * @param TransferFactoryInterface $transferFactory
     * @param ClientInterface $client
     * @param LoggerInterface $logger
     * @param Logger $paymentLogger
     * @param Data $helper
     * @param HandlerInterface|null $handler
     * @param ValidatorInterface|null $validator
     */
    public function __construct(
        BuilderInterface $requestBuilder,
        TransferFactoryInterface $transferFactory,
        ClientInterface $client,
        LoggerInterface $logger,
        Logger $paymentLogger,
        Data $helper,
        HandlerInterface $handler = null,
        ValidatorInterface $validator = null
    ) {
        $this->requestBuilder  = $requestBuilder;
        $this->transferFactory = $transferFactory;
        $this->client          = $client;
        $this->logger          = $logger;
        $this->handler         = $handler;
        $this->validator       = $validator;
        $this->paymentLogger   = $paymentLogger;
        $this->helper          = $helper;
    }

    /**
     * Executes command basing on business object
     *
     * @param array $commandSubject
     *
     * @return void
     * @throws CommandException
     * @throws ClientException
     * @throws ConverterException
     * @throws Exception
     */
    public function execute(array $commandSubject)
    {
        $payment = $this->helper->getValidPaymentInstance($commandSubject);

        if ($isHosted = $payment->getAdditionalInformation('hostedResponse')) {
            $response = $isHosted;
        } else {
            $transfer = $this->transferFactory->create($this->requestBuilder->build($commandSubject));

            $this->paymentLogger->debug(['barclaycard request' => $transfer->getBody()]);

            $response = $this->client->placeRequest($transfer);
        }

        $this->paymentLogger->debug(['barclaycard response' => $response]);

        if ($this->validator) {
            $result = $this->validator->validate(array_merge($commandSubject, ['response' => $response]));

            if (!$result->isValid()) {
                $exceptions = $result->getFailsDescription();

                foreach ($exceptions as $exception) {
                    $this->logger->critical((string) $exception);
                }

                if (count($exceptions)) {
                    throw new CommandException(__(implode('. ', $exceptions)));
                }
            }
        }

        if ($this->handler) {
            $this->handler->handle($commandSubject, $response);
        }
    }
}
