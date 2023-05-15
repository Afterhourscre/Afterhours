<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Model\ResourceModel\Rule\ProductType\Operation\Stock;

use Magento\Catalog\Api\Data\ProductInterface;
use Aheadworks\OnSale\Model\ResourceModel\Rule\ProductType\AttributeProcessorInterface;
use Magento\CatalogInventory\Model\Stock\Status;
use Magento\GroupedProduct\Model\ResourceModel\Product\Link;

/**
 * Class Grouped
 *
 * @package Aheadworks\OnSale\Model\ResourceModel\Rule\ProductType\Operation\Stock
 */
class Grouped extends Link implements AttributeProcessorInterface
{
    /**
     * {@inheritdoc}
     */
    public function process($product)
    {
        $productId = $product->getData(AttributeProcessorInterface::PRODUCT_ENTITY_ID);
        $connection = $this->getConnection();

        $select = $this->prepareSelect();
        $select->where(
            'cpe_grouped.entity_id = :product_id AND tbl_main.link_type_id = :link_type_id'
        );

        $qty = $connection->fetchCol($select, ['product_id' => $productId, 'link_type_id' => self::LINK_TYPE_GROUPED]);
        return (int) reset($qty);
    }

    /**
     * {@inheritdoc}
     */
    public function getSelectForIndexing()
    {
        $select = $this->prepareSelect();
        $select->where(
            'cpe_grouped.entity_id = e.entity_id AND tbl_main.link_type_id = ?',
            self::LINK_TYPE_GROUPED
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
            ['cpe_grouped' => $this->getTable('catalog_product_entity')],
            'cpe_grouped.' . $linkField .' = tbl_main.product_id',
            []
        )->join(
            ['tbl_stock' => $this->getTable('cataloginventory_stock_status')],
            'tbl_main.linked_product_id = tbl_stock.product_id',
            []
        )->where('tbl_stock.stock_status = ?', Status::STATUS_IN_STOCK);
    }
}
