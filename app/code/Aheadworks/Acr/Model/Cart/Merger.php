<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Acr\Model\Cart;

use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Quote\Api\CartManagementInterface;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class CartRestorer
 * @package Aheadworks\Acr\Model
 */
class Merger
{
    /**
     * @var CartRepositoryInterface
     */
    private $cartRepository;

    /**
     * @var CheckoutSession
     */
    private $checkoutSession;

    /**
     * @var CartManagementInterface
     */
    private $cartManagement;

    /**
     * @param CartRepositoryInterface $cartRepository
     * @param CheckoutSession $checkoutSession
     * @param CartManagementInterface $cartManagement
     */
    public function __construct(
        CartRepositoryInterface $cartRepository,
        CheckoutSession $checkoutSession,
        CartManagementInterface $cartManagement
    ) {
        $this->cartRepository = $cartRepository;
        $this->checkoutSession = $checkoutSession;
        $this->cartManagement = $cartManagement;
    }

    /**
     * Merge current cart with another cart by id
     *
     * @param $cartId
     * @param $currentCart
     * @param $customerId
     * @throws NoSuchEntityException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function mergeCartById($cartId, $currentCart, $customerId)
    {
        $cart = $this->cartRepository->get($cartId);

        if ($currentCart->getId() == $cart->getId()) {
            throw new \Exception(__('Current cart can not be restored'));
        }

        if (!$currentCart->getItemsCount()) {
            if ($customerId) {
                $currentCartId = $this->cartManagement->createEmptyCartForCustomer($customerId);
            } else {
                $currentCartId = $this->cartManagement->createEmptyCart();
            }
            $currentCart = $this->cartRepository->get($currentCartId);
        }
        $currentCart->merge($cart)->collectTotals();
        $this->cartRepository->save($currentCart);
        $this->checkoutSession->replaceQuote($currentCart);
    }
}
