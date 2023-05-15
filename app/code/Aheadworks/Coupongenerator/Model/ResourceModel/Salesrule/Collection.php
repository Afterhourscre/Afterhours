<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Coupongenerator\Model\ResourceModel\Salesrule;

use Magento\SalesRule\Api\Data\RuleInterface;
use Aheadworks\Coupongenerator\Model\Salesrule as SalesruleModel;
use Aheadworks\Coupongenerator\Model\ResourceModel\Salesrule as SalesruleResource;

/**
 * Class Collection
 * @package Aheadworks\Coupongenerator\Model\ResourceModel\Salesrule
 * @codeCoverageIgnore
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'id';

    /**
     * @var \Magento\Framework\EntityManager\MetadataPool
     */
    protected $metadataPool;

    /**
     * @param \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Framework\EntityManager\MetadataPool $metadataPool
     */
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\EntityManager\MetadataPool $metadataPool
    ) {
        $this->metadataPool = $metadataPool;
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager);
    }

    /**
     * {@inheritdoc}
     */
    public function _construct()
    {
        $this->_init(
            SalesruleModel::class,
            SalesruleResource::class
        );
    }

    /**
     * Init collection select
     *
     * @return $this
     */
    protected function _initSelect()
    {
        $magentoSalesruleQuery = $this->getMagentoSalesruleQuery();
        $this->getSelect()
            ->from(
                ['main_table' => $this->getMainTable()]
            )
            ->joinLeft(
                [
                    'msr' => new \Zend_Db_Expr(
                        "({$magentoSalesruleQuery})"
                    )
                ],
                "main_table.rule_id = msr.rule_id",
                [
                    "name",
                    "is_active",
                    "websites",
                    "website_ids",
                    "coupons_generated",
                    "usage_rate",
                    "coupons_times_used"
                ]
            )
        ;

        return $this;
    }

    /**
     * Get sales rule query
     *
     * @return \Magento\Framework\DB\Select
     */
    protected function getMagentoSalesruleQuery()
    {
        $linkField = $this->metadataPool->getMetadata(RuleInterface::class)->getLinkField();

        $connection = $this->getConnection();
        $select = $connection->select()
            ->from(
                ['msr' => $this->getTable('salesrule')],
                [
                    "msr.rule_id",
                    "msr.name",
                    "msr.is_active",
                    new \Zend_Db_Expr("GROUP_CONCAT(website.name SEPARATOR ', ') as websites"),
                    new \Zend_Db_Expr("GROUP_CONCAT(msrw.website_id SEPARATOR ',') as website_ids"),

                    new \Zend_Db_Expr("IFNULL(msrc.count, 0) as coupons_generated"),
                    new \Zend_Db_Expr(
                        "ROUND(IFNULL(IFNULL(msrc.coupons_times_used, 0) / IFNULL(msrc.count, 0) *100, 0), 0)" .
                        " as usage_rate"
                    ),
                ]
            )
            ->join(
                ['msrw' => $this->getTable('salesrule_website')],
                "msr.rule_id = msrw.{$linkField}",
                []
            )
            ->join(
                ["website" => $this->getTable('store_website')],
                "website.website_id = msrw.website_id",
                []
            )
            ->join(
                ["awsr" => $this->getTable('aw_coupongenerator_salesrule')],
                "awsr.rule_id = msr.rule_id",
                []
            )
            ->joinLeft(
                [
                    'msrc' =>
                        new \Zend_Db_Expr(
                            "(SELECT rule_id, count(*) as count, sum(case when (times_used > 0) then 1 else 0 END)" .
                            " as coupons_times_used FROM {$this->getTable('salesrule_coupon')} GROUP BY rule_id)"
                        )
                ],
                "msrc.rule_id = msr.rule_id",
                ["coupons_times_used" => "IFNULL(coupons_times_used, 0)"]
            )
            ->group('msr.rule_id')
        ;

        return $select;
    }

    /**
     * Retrieve option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        $rules = parent::_toOptionArray('id', 'name');
        if (!count($rules)) {
            array_unshift(
                $rules,
                [
                    'value' => 0,
                    'label' => __('No active rules found')
                ]
            );
        }
        return $rules;
    }

    /**
     * Set active rules filter
     *
     * @return $this
     */
    public function setActiveRules()
    {
        $this->addFieldToFilter(
            'is_active',
            ['eq' => \Aheadworks\Coupongenerator\Model\Source\Rule\Status::STATUS_ACTIVE]
        );

        return $this;
    }

    /**
     * Add webdite filter
     *
     * @param int $websiteId
     * @return $this
     */
    public function addWebsiteFilter($websiteId)
    {
        $this->addFieldToFilter(
            'website_ids',
            ['finset' => $websiteId]
        );

        return $this;
    }
}
