<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Acr\Model\Rule;

use Aheadworks\Acr\Api\Data\RuleInterface;
use Aheadworks\Acr\Model\Rule\Cart as CartRule;
use Aheadworks\Acr\Model\Rule\CartFactory as CartRuleFactory;
use Aheadworks\Acr\Model\Rule\Product as ProductRule;
use Aheadworks\Acr\Model\Rule\ProductFactory as ProductRuleFactory;

class Converter
{
    /**
     * @var CartRuleFactory
     */
    private $cartRuleFactory;

    /**
     * @var ProductRuleFactory
     */
    private $productRuleFactory;

    /**
     * @param CartFactory $cartRuleFactory
     * @param ProductFactory $productRuleFactory
     */
    public function __construct(
        CartRuleFactory $cartRuleFactory,
        ProductRuleFactory $productRuleFactory
    ) {
        $this->cartRuleFactory= $cartRuleFactory;
        $this->productRuleFactory = $productRuleFactory;
    }

    /**
     * Get cart rule
     *
     * @param RuleInterface $rule
     * @return CartRule
     */
    public function getCartRule(RuleInterface $rule)
    {
        /** @var CartRule $cartRule */
        $cartRule = $this->cartRuleFactory->create();
        $cartRule->setData('conditions_serialized', $rule->getCartConditions());
        return $cartRule;
    }

    /**
     * Get product rule
     *
     * @param RuleInterface $rule
     * @return ProductRule
     */
    public function getProductRule(RuleInterface $rule)
    {
        /** @var ProductRule $productRule */
        $productRule = $this->productRuleFactory->create();
        $productRule->setData('conditions_serialized', $rule->getProductConditions());
        return $productRule;
    }

    /**
     * Get cart conditions from submitted form data
     *
     * @param array $data
     * @return string
     */
    public function getCartConditions($data)
    {
        $cartCondSerialized = '';
        $ruleData = $this->explodeRuleData($data);

        if (isset($ruleData['cartRule'])) {
            /** @var  CartRule $cartRule */
            $cartRule = $this->cartRuleFactory->create();
            $cartRule->loadPost($ruleData['cartRule']);
            if ($cartRule->getConditions()) {
                $cartCondSerialized = json_encode($cartRule->getConditions()->asArray());
            }
        }

        return $cartCondSerialized;
    }

    /**
     * Get product conditions from submitted form data
     *
     * @param array $data
     * @return string
     */
    public function getProductConditions($data)
    {
        $productCondSerialized = '';
        $ruleData = $this->explodeRuleData($data);

        if (isset($ruleData['productRule'])) {
            /** @var  ProductRule $productRule */
            $productRule = $this->productRuleFactory->create();
            $productRule->loadPost($ruleData['productRule']);
            if ($productRule->getConditions()) {
                $productCondSerialized = json_encode($productRule->getConditions()->asArray());
            }
        }

        return $productCondSerialized;
    }

    /**
     * Explode rule data from submitted rule data
     *
     * @param array $data
     * @return array
     */
    protected function explodeRuleData($data)
    {
        $result = [];

        $types = [
            'cartRule' => '1',
            'productRule' => '2'
        ];
        foreach ($data['conditions'] as $key => $value) {
            if (substr($key, 0, 1) == $types['cartRule']) {
                $result['cartRule']['conditions'][$key] = $value;
            } elseif (substr($key, 0, 1) == $types['productRule']) {
                $result['productRule']['conditions']['1' . substr($key, 1)] = $value;
            }
        }
        return $result;
    }
}
