<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Coupongenerator\Model\ResourceModel\Coupon;

use Aheadworks\Coupongenerator\Model\Coupon as CouponModel;
use Aheadworks\Coupongenerator\Model\ResourceModel\Coupon as CouponResource;

/**
 * Class Collection
 * @package Aheadworks\Coupongenerator\Model\Resource\Coupon
 * @codeCoverageIgnore
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'id';

    /**
     * {@inheritdoc}
     */
    public function _construct()
    {
        $this->_init(
            CouponModel::class,
            CouponResource::class
        );
    }
}
