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

namespace Mageplaza\Barclaycard\Controller;

use Exception;
use InvalidArgumentException;
use Magento\Checkout\Helper\Data;
use Magento\Checkout\Model\Session;
use Magento\Checkout\Model\Type\Onepage;
use Magento\Customer\Model\Group;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\CartManagementInterface;
use Magento\Quote\Model\Quote;
use Mageplaza\Barclaycard\Helper\Response;
use Psr\Log\LoggerInterface;

/**
 * Class PlaceOrder
 * @package Mageplaza\Barclaycard\Controller
 */
abstract class PlaceOrder extends Action
{
    /**
     * @var Session
     */
    private $checkoutSession;

    /**
     * @var CartManagementInterface
     */
    private $cartManagement;

    /**
     * @var Session
     */
    private $customerSession;

    /**
     * @var Data
     */
    private $checkoutHelper;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var Response
     */
    protected $helper;

    /**
     * PlaceOrder constructor.
     *
     * @param Context $context
     * @param CartManagementInterface $cartManagement
     * @param Data $checkoutHelper
     * @param Session $checkoutSession
     * @param CustomerSession $customerSession
     * @param LoggerInterface $logger
     * @param Response $helper
     */
    public function __construct(
        Context $context,
        CartManagementInterface $cartManagement,
        Data $checkoutHelper,
        Session $checkoutSession,
        CustomerSession $customerSession,
        LoggerInterface $logger,
        Response $helper
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->cartManagement  = $cartManagement;
        $this->checkoutHelper  = $checkoutHelper;
        $this->customerSession = $customerSession;
        $this->logger          = $logger;
        $this->helper          = $helper;

        parent::__construct($context);
    }

    /**
     * @return ResponseInterface|Redirect|ResultInterface
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function execute()
    {
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $quote          = $this->checkoutSession->getQuote();

        try {
            if (!$quote || !$quote->getItemsCount()) {
                throw new InvalidArgumentException(__('We can\'t initialize checkout.'));
            }
            if ($this->getCheckoutMethod($quote) === Onepage::METHOD_GUEST) {
                $this->prepareGuestQuote($quote);
            }

            $this->disabledQuoteAddressValidation($quote);

            $quote->collectTotals();

            $this->paymentHandler($quote);

            $this->cartManagement->placeOrder($quote->getId());

            return $resultRedirect->setPath('checkout/onepage/success', ['_secure' => true]);
        } catch (Exception $e) {
            $this->logger->critical($e);
            $this->messageManager->addExceptionMessage($e, $e->getMessage());
        }

        return $resultRedirect->setPath('checkout/cart', ['_secure' => true]);
    }

    /**
     * @param Quote $quote
     *
     * @return Quote
     */
    protected function paymentHandler($quote)
    {
        return $quote;
    }

    /**
     * Make sure addresses will be saved without validation errors
     *
     * @param Quote $quote
     *
     * @return void
     */
    protected function disabledQuoteAddressValidation(Quote $quote)
    {
        $billingAddress = $quote->getBillingAddress();
        $billingAddress->setShouldIgnoreValidation(true);

        if (!$quote->getIsVirtual()) {
            $shippingAddress = $quote->getShippingAddress();
            $shippingAddress->setShouldIgnoreValidation(true);
            if (!$billingAddress->getEmail()) {
                $billingAddress->setSameAsBilling(1);
            }
        }
    }

    /**
     * Get checkout method
     *
     * @param Quote $quote
     *
     * @return string
     */
    protected function getCheckoutMethod(Quote $quote)
    {
        if ($this->customerSession->isLoggedIn()) {
            return Onepage::METHOD_CUSTOMER;
        }
        if (!$quote->getCheckoutMethod()) {
            if ($this->checkoutHelper->isAllowedGuestCheckout($quote)) {
                $quote->setCheckoutMethod(Onepage::METHOD_GUEST);
            } else {
                $quote->setCheckoutMethod(Onepage::METHOD_REGISTER);
            }
        }

        return $quote->getCheckoutMethod();
    }

    /**
     * Prepare quote for guest checkout order submit
     *
     * @param Quote $quote
     *
     * @return void
     */
    protected function prepareGuestQuote(Quote $quote)
    {
        $quote->setCustomerId(null)
            ->setCustomerEmail($quote->getBillingAddress()->getEmail())
            ->setCustomerIsGuest(true)
            ->setCustomerGroupId(Group::NOT_LOGGED_IN_ID);
    }
}
