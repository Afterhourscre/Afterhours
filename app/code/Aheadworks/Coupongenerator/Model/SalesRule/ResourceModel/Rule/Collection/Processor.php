<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Coupongenerator\Model\SalesRule\ResourceModel\Rule\Collection;

use Magento\SalesRule\Model\ResourceModel\Rule\Collection as SalesRuleCollection;

/**
 * Class Processor
 * @package Aheadworks\Coupongenerator\Model\SalesRule\ResourceModel\Rule\Collection
 */
class Processor implements ProcessorInterface
{
    /**
     * Collection flag to set after applying additional validation
     */
    const AW_CCG_VALIDATION_FILTER_FLAG_NAME = 'awcg';

    /**
     * {@inheritdoc}
     */
    public function updateValidationFilter(SalesRuleCollection $collection, $couponCode)
    {
        if ($collection->getFlag('validation_filter')
            && !$collection->getFlag(self::AW_CCG_VALIDATION_FILTER_FLAG_NAME)
        ) {
            $this->addCouponCodeGeneratorValidation($collection, $couponCode);
            $collection->setFlag(self::AW_CCG_VALIDATION_FILTER_FLAG_NAME, true);
        }

        return $collection;
    }

    /**
     * Add CCG-specific validation conditions
     *
     * @param SalesRuleCollection $collection
     * @param string $couponCode
     * @return SalesRuleCollection
     */
    protected function addCouponCodeGeneratorValidation(SalesRuleCollection $collection, $couponCode)
    {
        if (strlen($couponCode)) {
            $select = $collection->getConnection()->select()->from(
                ['main_table' => $collection->getTable('salesrule')],
                'rule_id'
            )->joinLeft(
                ['rule_coupons' => $collection->getTable('salesrule_coupon')],
                'main_table.rule_id = rule_coupons.rule_id',
                []
            )->joinLeft(
                ['aw_cg_coupons' => $collection->getTable('aw_coupongenerator_coupon')],
                'aw_cg_coupons.coupon_id = rule_coupons.coupon_id',
                []
            )->where(
                'aw_cg_coupons.is_deactivated = 1'
            )->where(
                'rule_coupons.code = ?',
                $couponCode
            )->group('main_table.rule_id');
            $deactivatedCouponsRuleId = (string)$collection->getConnection()->fetchOne($select);

            if (!empty($deactivatedCouponsRuleId)) {
                $collection->getSelect()
                    ->where(
                        $this->getMainTableAlias($collection) . '.rule_id NOT IN (?)',
                        $deactivatedCouponsRuleId
                    );
            }
        }
        return $collection;
    }

    /**
     * Retrieve main table alias
     *
     * @param SalesRuleCollection $collection
     * @return string
     */
    private function getMainTableAlias($collection)
    {
        $result = 'main_table';

        foreach ($collection->getSelect()->getPart(\Zend_Db_Select::FROM) as $alias => $data) {
            if (isset($data['joinType']) && $data['joinType'] == \Zend_Db_Select::FROM) {
                $result = $alias;
            }
        }

        return $result;
    }
}
