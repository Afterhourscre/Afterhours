<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Coupongenerator\Model\ResourceModel\Coupon\Grid;

use Magento\Framework\Api\Search\AggregationInterface;
use Magento\Framework\Api\Search\SearchResultInterface;
use Aheadworks\Coupongenerator\Model\ResourceModel\Coupon\Collection as CouponCollection;
use Magento\SalesRule\Api\Data\RuleInterface;
use Aheadworks\Coupongenerator\Ui\Component\DataProvider\Coupon\Document as DataProviderDocument;

/**
 * Class Collection
 * Collection for displaying grid
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @codeCoverageIgnore
 */
class Collection extends CouponCollection implements SearchResultInterface
{
    /**
     * @var AggregationInterface
     */
    private $aggregations;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    private $date;

    /**
     * @var \Magento\Framework\Stdlib\DateTime
     */
    private $dateTime;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private $request;

    /**
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param \Magento\Framework\Stdlib\DateTime $dateTime
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param mixed|null $mainTable
     * @param \Magento\Framework\Model\ResourceModel\Db\AbstractDb $eventPrefix
     * @param mixed $eventObject
     * @param mixed $resourceModel
     * @param string $model
     * @param null $connection
     * @param \Magento\Framework\Model\ResourceModel\Db\AbstractDb|null $resource
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Framework\Stdlib\DateTime $dateTime,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        $mainTable,
        $eventPrefix,
        $eventObject,
        $resourceModel,
        $model = DataProviderDocument::class,
        $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
        $this->_eventPrefix = $eventPrefix;
        $this->_eventObject = $eventObject;
        $this->_init($model, $resourceModel);
        $this->setMainTable($mainTable);
        $this->date = $date;
        $this->dateTime = $dateTime;
        $this->request = $request;

        $this->joinOtherFields();
    }

    /**
     * Join additional fields to collection
     *
     * @return $this
     */
    private function joinOtherFields()
    {
        if (!$this->getFlag('other_fields_joined')) {
            $couponWithStatusQuery = $this->getCouponWithStatusQuery();
            $customerQuery = $this->getCustomerQuery();
            $buyXGetYAction = RuleInterface::DISCOUNT_ACTION_BUY_X_GET_Y;

            $this->getSelect()
                ->joinLeft(
                    [
                        'msrc' =>
                            new \Zend_Db_Expr(
                                "({$couponWithStatusQuery})"
                            )
                    ],
                    "msrc.coupon_id = main_table.coupon_id",
                    [
                        "code",
                        "times_used",
                        "created_at",
                        "expiration_date",
                        "status"
                    ]
                )
                ->joinLeft(
                    [
                        'msr' =>
                            new \Zend_Db_Expr(
                                "(SELECT rule_id, `name`, simple_action, discount_amount, discount_step, ".
                                "(CASE WHEN simple_action = '{$buyXGetYAction}' THEN ".
                                "CONCAT('Buy ', discount_step,' Get ', discount_amount) ELSE discount_amount END) ".
                                "as discount_amount_str FROM {$this->getTable('salesrule')})"
                            )
                    ],
                    "msrc.rule_id = msr.rule_id",
                    [
                        "msr.name as rule_name",
                        "msr.simple_action",
                        "msr.discount_amount",
                        "msr.discount_amount_str",
                        "msr.discount_step"
                    ]
                )
                ->joinLeft(
                    [
                        'ce' =>
                            new \Zend_Db_Expr(
                                "({$customerQuery})"
                            )
                    ],
                    "main_table.coupon_id = ce.coupon_id",
                    [
                        "customer",
                        "customer_name"
                    ]
                )
                ->joinLeft(
                    [
                        "au" =>
                            new \Zend_Db_Expr(
                                "(SELECT CONCAT_WS(' ', firstname, lastname) as created_by, user_id "
                                . "FROM {$this->getTable('admin_user')})"
                            )
                    ],
                    "main_table.admin_user_id = au.user_id"
                )
                ->joinLeft(
                    ['awsr' => $this->getTable('aw_coupongenerator_salesrule')],
                    'awsr.rule_id = msrc.rule_id',
                    ['rule_id' => 'id']
                )
            ;

            $this->addFilterToMap('code', 'msrc.code');
            $this->addFilterToMap('times_used', 'msrc.times_used');
            $this->addFilterToMap('created_at', 'msrc.created_at');
            $this->addFilterToMap('expiration_date', 'msrc.expiration_date');
            $this->addFilterToMap('discount_amount', 'msr.discount_amount');
            $this->addFilterToMap('rule_id', 'awsr.id');
            $this->addFilterToMap('created_by', 'au.created_by');
            $this->setFlag('other_fields_joined', true);
        }
        return $this;
    }

    /**
     * Get coupon with status query
     *
     * @return \Magento\Framework\DB\Select
     */
    private function getCouponWithStatusQuery()
    {
        $expired = \Aheadworks\Coupongenerator\Model\Source\Coupon\Status::EXPIRED_VALUE;
        $used = \Aheadworks\Coupongenerator\Model\Source\Coupon\Status::USED_VALUE;
        $available = \Aheadworks\Coupongenerator\Model\Source\Coupon\Status::AVAILABLE_VALUE;
        $deactivated = \Aheadworks\Coupongenerator\Model\Source\Coupon\Status::DEACTIVATED_VALUE;
        $nowTimestamp = $this->dateTime->formatDate($this->date->gmtTimestamp());
        $dateNow = (new \DateTime($nowTimestamp))->format('Y-m-d H:i:s');

        $connection = $this->getConnection();
        $select = $connection->select()
            ->from(
                ['awc' => $this->getMainTable()],
                []
            )
            ->joinLeft(
                ['src' => $this->getTable('salesrule_coupon')],
                "src.coupon_id = awc.coupon_id",
                [
                    "coupon_id",
                    "code",
                    "rule_id",
                    "times_used",
                    "created_at",
                    "expiration_date",
                    new \Zend_Db_Expr(
                        "(SELECT CASE WHEN `awc`.is_deactivated = '1' THEN '{$deactivated}' "
                        . "WHEN times_used >= usage_limit AND times_used > 0 AND usage_limit != 0 THEN '{$used}'"
                        . "WHEN expiration_date <= '{$dateNow}' THEN '{$expired}' "
                        . "ELSE '{$available}' END) AS status"
                    )
                ]
            );

        return $select;
    }

    /**
     * Get customer query
     *
     * @return \Magento\Framework\DB\Select
     */
    private function getCustomerQuery()
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from(
                ['awc' => $this->getMainTable()],
                [
                    'coupon_id',
                    new \Zend_Db_Expr(
                        "CONCAT_WS(' ', ce.firstname, ce.lastname, awc.recipient_email) as customer"
                    )
                ]
            )
            ->joinLeft(
                ['ce' => $this->getTable('customer_entity')],
                "awc.customer_id = ce.entity_id",
                [
                    new \Zend_Db_Expr(
                        "CONCAT_WS(' ', ce.firstname, ce.lastname) as customer_name"
                    )
                ]
            );

        return $select;
    }

    /**
     * @return AggregationInterface
     */
    public function getAggregations()
    {
        return $this->aggregations;
    }

    /**
     * @param AggregationInterface $aggregations
     * @return $this
     */
    public function setAggregations($aggregations)
    {
        $this->aggregations = $aggregations;
        return $this;
    }

    /**
     * Get search criteria.
     *
     * @return \Magento\Framework\Api\SearchCriteriaInterface|null
     */
    public function getSearchCriteria()
    {
        return null;
    }

    /**
     * Set search criteria
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setSearchCriteria(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria = null)
    {
        return $this;
    }

    /**
     * Get total count
     *
     * @return int
     */
    public function getTotalCount()
    {
        return $this->getSize();
    }

    /**
     * Set total count
     *
     * @param int $totalCount
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setTotalCount($totalCount)
    {
        return $this;
    }

    /**
     * Set items list
     *
     * @param \Magento\Framework\Api\ExtensibleDataInterface[] $items
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setItems(array $items = null)
    {
        return $this;
    }

    /**
     * Set collection page size
     *
     * @param   int $size
     * @return $this
     */
    public function setPageSize($size)
    {
        if ($this->request->getControllerName() == 'export') {
            $this->_pageSize = $this->getSize();
        } else {
            $this->_pageSize = $size;
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addFieldToFilter($field, $condition = null)
    {
        if ($field == 'id') {
            $field = 'main_table.id';
        }
        parent::addFieldToFilter($field, $condition);
    }
}
