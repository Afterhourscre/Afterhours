<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Coupongenerator\Model\ResourceModel;

/**
 * Class Coupon
 * @package Aheadworks\Coupongenerator\Model\ResourceModel
 * @codeCoverageIgnore
 */
class Coupon extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param null $resourcePrefix
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        $resourcePrefix = null
    ) {
        parent::__construct($context, $resourcePrefix);
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('aw_coupongenerator_coupon', 'id');
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function _getLoadSelect($field, $value, $object)
    {
        $select = parent::_getLoadSelect($field, $value, $object);

        $select
            ->joinLeft(
                ['msrc' => $this->getTable('salesrule_coupon')],
                "{$this->getMainTable()}.coupon_id = msrc.coupon_id",
                [
                    "msrc.rule_id",
                    "msrc.code",
                    "msrc.usage_limit",
                    "msrc.expiration_date"
                ]
            )
        ;
        return $select;
    }
}
