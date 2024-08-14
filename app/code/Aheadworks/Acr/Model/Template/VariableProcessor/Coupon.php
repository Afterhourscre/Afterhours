<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Acr\Model\Template\VariableProcessor;

use Aheadworks\Acr\Api\CouponVariableManagementInterface;
use Magento\Store\Model\StoreManagerInterface;
use Aheadworks\Acr\Model\Source\Email\Variables;

/**
 * Class Coupon
 *
 * @package Aheadworks\Acr\Model\Template\VariableProcessor
 */
class Coupon implements VariableProcessorInterface
{
    /**
     * @var CouponVariableManagementInterface
     */
    private $couponVariableManager;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param CouponVariableManagementInterface $couponVariableManager
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        CouponVariableManagementInterface $couponVariableManager,
        StoreManagerInterface $storeManager
    ) {
        $this->couponVariableManager = $couponVariableManager;
        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function process($quote, $params)
    {
        return [Variables::COUPON => $this->couponVariableManager->getCouponVariable(
            $params['rule_id'],
            $params['store_id']
        )];
    }

    /**
     * {@inheritdoc}
     */
    public function processTest($params)
    {
        return [Variables::COUPON => $this->couponVariableManager->getTestCouponVariable()];
    }
}
