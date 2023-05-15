<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Coupongenerator\Model\ResourceModel;

use Magento\SalesRule\Api\Data\RuleInterface;

/**
 * Class Salesrule
 * @package Aheadworks\Coupongenerator\Model\ResourceModel
 * @codeCoverageIgnore
 */
class Salesrule extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * @var \Magento\Framework\EntityManager\MetadataPool
     */
    protected $metadataPool;

    /**
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param \Magento\Framework\EntityManager\MetadataPool $metadataPool
     * @param null $resourcePrefix
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Magento\Framework\EntityManager\MetadataPool $metadataPool,
        $resourcePrefix = null
    ) {
        $this->metadataPool = $metadataPool;
        parent::__construct($context, $resourcePrefix);
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('aw_coupongenerator_salesrule', 'id');
    }

    /**
     * @param string $field
     * @param mixed $value
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return \Magento\Framework\DB\Select
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function _getLoadSelect($field, $value, $object)
    {
        $linkField = $this->metadataPool->getMetadata(RuleInterface::class)->getLinkField();
        $select = parent::_getLoadSelect($field, $value, $object);

        $select
            ->joinLeft(
                ['msr' => $this->getTable('salesrule')],
                "{$this->getMainTable()}.rule_id = msr.rule_id",
                [
                    "msr.name",
                    "msr.uses_per_customer",
                    "msr.is_active",
                    "msr.times_used",
                    "msr.uses_per_coupon",
                    "msr.coupon_type",
                    "msr.simple_action",
                    "msr.discount_amount",
                ]
            )
            ->join(
                ['msrw' => $this->getTable('salesrule_website')],
                "{$this->getMainTable()}.rule_id = msrw.{$linkField}",
                [
                    new \Zend_Db_Expr("GROUP_CONCAT(`msrw`.`website_id`) as 'website_ids'")
                ]
            )
        ;
        return $select;
    }

    /**
     * Get salesrule data by rule Id or array of rule Ids
     *
     * @param int|array $rule;
     * @return array
     */
    public function getByRuleId($rule)
    {
        $select = $this->getConnection()->select()
            ->from($this->getMainTable())
            ->where("{$this->getMainTable()}.rule_id IN (?)", $rule)
        ;
        if (is_array($rule)) {
            $data = $this->getConnection()->fetchAll($select);
        } else {
            $data = $this->getConnection()->fetchRow($select);
        }
        return $data;
    }

    /**
     * Save salesrule data
     *
     * @param array $rule
     * @return void
     */
    public function saveSalesrule($rule)
    {
        $this->getConnection()->insertOnDuplicate(
            $this->getMainTable(),
            [$rule],
            []
        );
    }
}
