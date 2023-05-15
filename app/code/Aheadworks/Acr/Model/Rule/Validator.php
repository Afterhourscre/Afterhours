<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Acr\Model\Rule;

use Aheadworks\Acr\Api\Data\RuleInterface;
use Aheadworks\Acr\Model\Rule\Converter as RuleConverter;
use Aheadworks\Acr\Model\Rule\Cart as CartRule;
use Aheadworks\Acr\Model\Rule\Product as ProductRule;
use Magento\Catalog\Model\Product as ProductModel;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Catalog\Api\Data\ProductInterface;

/**
 * Class Validator
 * @package Aheadworks\Acr\Model\Rule
 * @SuppressWarnings(PHPMD.CyclomaticComplexity)
 */
class Validator
{
    /**
     * @var RuleConverter
     */
    private $ruleConverter;

    /**
     * @var CartRepositoryInterface
     */
    private $cartRepository;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @param Converter $ruleConverter
     * @param CartRepositoryInterface $cartRepository
     * @param ProductRepositoryInterface $productRepository
     */
    public function __construct(
        RuleConverter $ruleConverter,
        CartRepositoryInterface $cartRepository,
        ProductRepositoryInterface $productRepository
    ) {
        $this->ruleConverter = $ruleConverter;
        $this->cartRepository = $cartRepository;
        $this->productRepository = $productRepository;
    }

    /**
     * Validate a cart history item by a rule
     *
     * @param RuleInterface $rule
     * @param array $cartData
     * @return bool
     */
    public function validate(RuleInterface $rule, $cartData)
    {
        if (!$this->validateStores($rule, $cartData)) {
            return false;
        }
        if (!$this->validateCustomerGroups($rule, $cartData)) {
            return false;
        }
        if (!$this->validateConditions($rule, $cartData)) {
            return false;
        }
        if (!$this->validateProductTypes($rule, $cartData)) {
            return false;
        }
        return true;
    }

    /**
     * Store views validation
     *
     * @param RuleInterface $rule
     * @param array $cartData
     * @return bool
     */
    private function validateStores(RuleInterface $rule, $cartData)
    {
        if (!in_array($cartData['store_id'], $rule->getStoreIds()) &&
            !in_array(0, $rule->getStoreIds())
        ) {
            return false;
        }
        return true;
    }

    /**
     * Customer groups validation
     *
     * @param RuleInterface $rule
     * @param array $cartData
     * @return bool
     */
    private function validateCustomerGroups(RuleInterface $rule, $cartData)
    {
        if (!in_array($cartData['customer_group_id'], $rule->getCustomerGroups()) &&
            !in_array('all', $rule->getCustomerGroups())
        ) {
            return false;
        }
        return true;
    }

    /**
     * Conditions validation
     *
     * @param RuleInterface $rule
     * @param array $cartData
     * @return bool
     */
    private function validateConditions(RuleInterface $rule, $cartData)
    {
        try {
            /** @var CartInterface|Quote $cart */
            $cart = $this->cartRepository->get($cartData['entity_id']);
            /** @var CartRule $cartRule */
            $cartRule = $this->ruleConverter->getCartRule($rule);
            /** @var ProductRule $productRule */
            $productRule = $this->ruleConverter->getProductRule($rule);

            if ($cartRule->getConditions()->getConditions()
                || $productRule->getConditions()->getConditions()
            ) {
                if ($cart->isVirtual()) {
                    $address = $cart->getBillingAddress();
                } else {
                    $address = $cart->getShippingAddress();
                }

                foreach ($address->getAllItems() as $item) {
                    /** @var ProductInterface|ProductModel $product */
                    $product = $this->productRepository->getById(
                        $item->getProductId(),
                        false,
                        $cartData['store_id'],
                        true
                    );
                    $item->setProduct($product);
                }
                if ($cartRule->getConditions()->getConditions()) {
                    $cart->collectTotals();
                    if (!$cartRule->validate($address)) {
                        return false;
                    }
                }
                if ($productRule->getConditions()->getConditions()) {
                    $valid = false;
                    foreach ($address->getAllItems() as $item) {
                        if ($productRule->validate($item->getProduct())) {
                            $valid = true;
                            break;
                        }
                    }
                    if (!$valid) {
                        return false;
                    }
                }
            }
        } catch (NoSuchEntityException $e) {
            return false;
        }
        return true;
    }

    /**
     * Product types validation
     *
     * @param RuleInterface $rule
     * @param array $cartData
     * @return bool
     */
    private function validateProductTypes(RuleInterface $rule, $cartData)
    {
        if (!in_array('all', $rule->getProductTypeIds())) {
            try {
                /** @var CartInterface|Quote $cart */
                $cart = $this->cartRepository->get($cartData['entity_id']);

                foreach ($cart->getItemsCollection() as $quoteItem) {
                    if (!$quoteItem->getParentItemId() &&
                        $quoteItem->hasData('product') &&
                        !in_array($quoteItem->getData('product')->getTypeId(), $rule->getProductTypeIds())
                    ) {
                        return false;
                    }
                }
            } catch (NoSuchEntityException $e) {
                return false;
            }
        }
        return true;
    }
}
