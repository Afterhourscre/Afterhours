<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Model\ResourceModel\Rule\ProductType\Operation\Stock;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\CatalogInventory\Model\Stock\Status;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Aheadworks\OnSale\Model\ResourceModel\Rule\ProductType\AttributeProcessorInterface;

/**
 * Class Configurable
 *
 * @package Aheadworks\OnSale\Model\ResourceModel\Rule\ProductType\Operation\Stock
 */
class Configurable extends AbstractDb implements AttributeProcessorInterface
{
    /**
     * @var MetadataPool
     */
    protected $metadataPool;

    /**
     * @param Context $context
     * @param MetadataPool $metadataPool
     * @param string $connectionName
     */
    public function __construct(
        Context $context,
        MetadataPool $metadataPool,
        $connectionName = null
    ) {
        $this->metadataPool = $metadataPool;
        parent::__construct($context, $connectionName);
    }

    /**
     * Init resource
     */
    protected function _construct()
    {
        $this->_init('catalog_product_super_link', 'link_id');
    }

    /**
     * {@inheritdoc}
     */
    public function process($product)
    {
        //@todo refactoring
        //find the common in the three queries (for bundle, configurable, grouped) and bring to a single view
        $productId = $product->getData(AttributeProcessorInterface::PRODUCT_ENTITY_ID);
        $connection = $this->getConnection();

        $select = $this->prepareSelect();
        $select->where('cpe_config_parent.entity_id IN (?)', $productId);

        $qty = $connection->fetchCol($select);
        return (int) reset($qty);
    }

    /**
     * {@inheritdoc}
     */
    public function getSelectForIndexing()
    {
        $select = $this->prepareSelect();
        $select->where(
            'cpe_config_parent.entity_id = e.entity_id'
        );

        return '(' . $select . ')';
    }

    /**
     * Prepare sql query for current product type
     *
     * @return \Magento\Framework\DB\Select
     * @throws \Exception
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function prepareSelect()
    {
        $connection = $this->getConnection();
        $linkField = $this->metadataPool->getMetadata(ProductInterface::class)->getLinkField();
        return $connection->select()->from(
            ['tbl_main' => $this->getMainTable()],
            ['qty' => 'SUM(tbl_stock.qty)']
        )->join(
            ['cpe_config_parent' => $this->getTable('catalog_product_entity')],
            'cpe_config_parent.' . $linkField . ' = tbl_main.parent_id',
            []
        )->join(
            ['cpe_config' => $this->getTable('catalog_product_entity')],
            'cpe_config.entity_id = tbl_main.product_id AND cpe_config.required_options = 0',
            []
        )->join(
            ['tbl_stock' => $this->getTable('cataloginventory_stock_status')],
            'cpe_config.entity_id = tbl_stock.product_id',
            []
        )->where('tbl_stock.stock_status = ?', Status::STATUS_IN_STOCK);
    }
}
