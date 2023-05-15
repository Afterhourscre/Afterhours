<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Coupongenerator\Ui\Component\DataProvider\Coupon;

use Aheadworks\Coupongenerator\Model\Source\Coupon\Status as CouponStatusSource;
use Magento\Framework\Api\AttributeValueFactory;

/**
 * Class Document
 * @package Aheadworks\Coupongenerator\Ui\Component\DataProvider\Coupon
 */
class Document extends \Magento\Framework\View\Element\UiComponent\DataProvider\Document
{
    /**
     * @var string
     */
    private static $statusAttributeCode = 'status';

    /**
     * @var CouponStatusSource
     */
    private $couponStatusSource;

    /**
     * @param AttributeValueFactory $attributeValueFactory
     * @param CouponStatusSource $couponStatusSource
     */
    public function __construct(
        AttributeValueFactory $attributeValueFactory,
        CouponStatusSource $couponStatusSource
    ) {
        parent::__construct($attributeValueFactory);
        $this->couponStatusSource = $couponStatusSource;
    }

    /**
     * @inheritdoc
     */
    public function getCustomAttribute($attributeCode)
    {
        if ($attributeCode == self::$statusAttributeCode) {
            $value = $this->getData(self::$statusAttributeCode);
            $this->setCustomAttribute(self::$statusAttributeCode, $this->couponStatusSource->getOptionByValue($value));
        }
        return parent::getCustomAttribute($attributeCode);
    }
}
