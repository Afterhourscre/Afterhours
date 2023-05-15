<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Model\ResourceModel\Rule\ProductType\Operation\Stock;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Bundle\Model\ResourceModel\Selection;
use Aheadworks\OnSale\Model\ResourceModel\Rule\ProductType\AttributeProcessorInterface;
use Magento\CatalogInventory\Model\Stock\Status;

/**
 * Class Bundle
 *
 * @package Aheadworks\OnSale\Model\ResourceModel\Rule\ProductType\Operation\Stock
 */
class Bundle extends Selection implements AttributeProcessorInterface
{
    /**
     * {@inheritdoc}
     */
    public function process($product)
    {
        $connection = $this->getConnection();
        $productId = $product->getData(AttributeProcessorInterface::PRODUCT_ENTITY_ID);

        $select = $this->prepareSelect();
        $select->where(
            'cpe_bundle_parent.entity_id = :parent_id'
        );

        $qty = $connection->fetchCol($select, ['parent_id' => $productId]);
        return (int) reset($qty);
    }

    /**
     * {@inheritdoc}
     */
    public function getSelectForIndexing()
    {
        $select = $this->prepareSelect();
        $select->where(
            'cpe_bundle_parent.entity_id = e.entity_id'
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
            ['tbl_selection' => $this->getMainTable()],
            ['qty' => 'SUM(tbl_stock.qty)']
        )->join(
            ['cpe_bundle' => $this->getTable('catalog_product_entity')],
            'cpe_bundle.entity_id = tbl_selection.product_id AND cpe_bundle.required_options = 0',
            []
        )->join(
            ['cpe_bundle_parent' => $this->getTable('catalog_product_entity')],
            'tbl_selection.parent_product_id = cpe_bundle_parent.' . $linkField,
            []
        )->join(
            ['tbl_option' => $this->getTable('catalog_product_bundle_option')],
            'tbl_option.option_id = tbl_selection.option_id',
            []
        )->join(
            ['tbl_stock' => $this->getTable('cataloginventory_stock_status')],
            'cpe_bundle.entity_id = tbl_stock.product_id',
            []
        )->where('tbl_stock.stock_status = ?', Status::STATUS_IN_STOCK);
    }
}
