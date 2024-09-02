<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-review
 * @version   1.1.2
 * @copyright Copyright (C) 2024 Mirasvit (https://mirasvit.com/)
 */


declare(strict_types=1);


namespace Mirasvit\Review\Model\ResourceModel\Review;


use Magento\Catalog\Api\Data\ProductAttributeInterface;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface as FetchStrategy;
use Magento\Framework\Data\Collection\EntityFactoryInterface as EntityFactory;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult;
use Mirasvit\Review\Api\Data\ReviewInterface;
use Psr\Log\LoggerInterface as Logger;


class Grid extends SearchResult
{
    protected $document = \Mirasvit\Review\Model\Review::class;

    public function __construct(
        EntityFactory $entityFactory,
        Logger        $logger,
        FetchStrategy $fetchStrategy,
        EventManager  $eventManager,
                      $mainTable = ReviewInterface::MAIN_TABLE,
                      $resourceModel = \Mirasvit\Review\Model\ResourceModel\Review::class
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $mainTable, $resourceModel);
    }

    protected function _initSelect()
    {
        parent::_initSelect();

        $subquery
            = 'SELECT ' . ProductAttributeInterface::ATTRIBUTE_ID . ' FROM ' . $this->getTable('eav_attribute') . '
             WHERE ' . $this->getTable('eav_attribute') . '.' . ProductAttributeInterface::ATTRIBUTE_CODE . ' = '
            . "'" . ProductAttributeInterface::CODE_NAME . "'" . ' AND ' . ProductAttributeInterface::FRONTEND_LABEL . ' = "Product Name"';

        $this->getSelect()
            ->joinLeft(
                ['detail' => $this->getTable(ReviewInterface::DETAIL_TABLE)],
                $this->getJoinConditionExpression('detail'),
                [
                    'detail',
                    'title',
                    'nickname',
                    'type' => new \Zend_Db_Expr('IF(customer_id is NOT NULL ,"Customer","Guest")'),
                ]
            )
            ->joinLeft(
                ['cpev' => $this->getTable('catalog_product_entity_varchar')],
                'main_table.' . ReviewInterface::ENTITY_ID . '=' . ReviewInterface::PRODUCT_ENTITY_ID . ' AND ' .
                'main_table.' . ReviewInterface::PRODUCT_ID . ' = ' . 'cpev' . '.entity_id' . ' AND ' .
                'cpev' . '.' . ProductAttributeInterface::ATTRIBUTE_ID . '= ' . '(' . $subquery . ') AND cpev.store_id = 0',
                'cpev.value AS product_name'
            )->joinLeft(
                ['cpe' => $this->getTable('catalog_product_entity')],
                'main_table.' . ReviewInterface::ENTITY_ID . '=' . ReviewInterface::PRODUCT_ENTITY_ID . ' AND ' .
                'main_table.' . ReviewInterface::PRODUCT_ID . ' = ' . 'cpe' . '.entity_id',
                'sku'
            )->joinLeft(
                ['detail_additional' => $this->getTable(ReviewInterface::DETAIL_ADDITIONAL_TABLE)],
                'detail_additional.' . ReviewInterface::ID . ' = main_table.' . ReviewInterface::ID,
                [ReviewInterface::IP, ReviewInterface::COUNTRY, ReviewInterface::LOCATION, ReviewInterface::IS_VERIFIED_BUYER]
            );


        return $this;
    }

    private function getJoinConditionExpression($tableAlias)
    {
        return 'main_table.' . ReviewInterface::ID . ' = ' . $tableAlias . '.' . ReviewInterface::ID;
    }

    public function getSelectCountSql()
    {
        $this->_renderFilters();

        $countSelect = clone $this->getSelect();
        $countSelect->reset(\Magento\Framework\DB\Select::ORDER);
        $countSelect->reset(\Magento\Framework\DB\Select::LIMIT_COUNT);
        $countSelect->reset(\Magento\Framework\DB\Select::LIMIT_OFFSET);

        $having = $this->getSelect()->getPart(\Magento\Framework\DB\Select::HAVING);

        if ($having) {
            $query = $countSelect->__toString();

            return "SELECT COUNT(*) FROM ($query) Review";
        } else {

            $countSelect->reset(\Magento\Framework\DB\Select::COLUMNS);

            $part = $this->getSelect()->getPart(\Magento\Framework\DB\Select::GROUP);

            if (!is_array($part) || !count($part)) {
                $countSelect->columns(new \Zend_Db_Expr('COUNT(*)'));

                return $countSelect;
            }

            $countSelect->reset(\Magento\Framework\DB\Select::GROUP);
            $group = $this->getSelect()->getPart(\Magento\Framework\DB\Select::GROUP);

            $countSelect->columns(new \Zend_Db_Expr(("COUNT(DISTINCT " . implode(", ", $group) . ")")));
        }

        return $countSelect;
    }

}
