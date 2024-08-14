<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Model\Rule\Condition\Product\Special;

use Magento\Framework\Model\AbstractModel;
use Magento\Rule\Model\Condition\Context as ConditionContext;
use Magento\Backend\Helper\Data as BackendHelperData;
use Magento\Eav\Model\Config as EavModelConfig;
use Magento\Catalog\Model\ProductFactory;
use Magento\Catalog\Model\ResourceModel\Product as ProductResourceModel;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\Collection as AttrSetCollection;
use Magento\Framework\Locale\FormatInterface;
use Aheadworks\OnSale\Model\Rule\Condition\Product\Special as SpecialProductCondition;
use Aheadworks\OnSale\Model\ResourceModel\Rule\ProductType\AttributeProcessor;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\EntityManager\MetadataPool;

/**
 * Class Stock
 *
 * @package Aheadworks\OnSale\Model\Rule\Condition\Product\Special
 */
class Stock extends SpecialProductCondition
{
    /**
     * Stock attribute used for conditions
     */
    const STOCK_ATTRIBUTE = 'qty';

    /**
     * Stock operation used for attribute processor
     */
    const STOCK_OPERATION = 'stock';

    /**
     * @var MetadataPool
     */
    protected $metadataPool;

    /**
     * @var AttributeProcessor
     */
    private $attributeProcessor;

    /**
     * @param ConditionContext $context
     * @param BackendHelperData $backendData
     * @param EavModelConfig $config
     * @param ProductFactory $productFactory
     * @param ProductRepositoryInterface $productRepository
     * @param ProductResourceModel $productResource
     * @param AttrSetCollection $attrSetCollection
     * @param FormatInterface $localeFormat
     * @param AttributeProcessor $attributeProcessor
     * @param MetadataPool $metadataPool
     * @param array $data
     */
    public function __construct(
        ConditionContext $context,
        BackendHelperData $backendData,
        EavModelConfig $config,
        ProductFactory $productFactory,
        ProductRepositoryInterface $productRepository,
        ProductResourceModel $productResource,
        AttrSetCollection $attrSetCollection,
        FormatInterface $localeFormat,
        AttributeProcessor $attributeProcessor,
        MetadataPool $metadataPool,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $backendData,
            $config,
            $productFactory,
            $productRepository,
            $productResource,
            $attrSetCollection,
            $localeFormat,
            $data
        );
        $this->setType(Stock::class);
        $this->metadataPool = $metadataPool;
        $this->attributeProcessor = $attributeProcessor;
    }

    /**
     * Validate product whether it meets conditions
     *
     * @param AbstractModel $model
     * @return bool
     */
    public function validate(AbstractModel $model)
    {
        $attrCode = self::STOCK_ATTRIBUTE;
        $oldAttrValue = $model->getData($attrCode);

        if ($oldAttrValue === null) {
            $qty = $this->attributeProcessor->prepareData(self::STOCK_OPERATION, $model);
            $model->setData($attrCode, $qty);
        }

        $result = $this->validateAttribute($model->getData($attrCode));
        return (bool)$result;
    }

    /**
     * Retrieve operator select options array
     *
     * @return array
     */
    private function getOperatorOptionArray()
    {
        return [
            '==' => __('equal to'),
            '>' => __('more than'),
            '>=' => __('equal or greater than'),
            '<' => __('less than'),
            '<=' => __('equal or less than')
        ];
    }

    /**
     * Set operator options
     *
     * @return $this
     */
    public function loadOperatorOptions()
    {
        parent::loadOperatorOptions();
        $this->setOperatorOption($this->getOperatorOptionArray());
        return $this;
    }

    /**
     * Retrieve rule as HTML formated string
     *
     * @return string
     */
    public function asHtml()
    {
        return $this->getTypeElementHtml() . __(
            'Stock Range is %1 %2',
            $this->getOperatorElementHtml(),
            $this->getValueElementHtml()
        ) . $this->getRemoveLinkHtml();
    }

    /**
     * Collect valid attributes
     *
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $productCollection
     * @return $this
     * @throws \Exception
     */
    public function collectValidatedAttributes($productCollection)
    {
        if (!$productCollection->getFlag('aw_onsale_collection_stock_joined')) {
            $linkField = $this->metadataPool->getMetadata(ProductInterface::class)->getLinkField();
            $productCollection->getSelect()
                ->join(
                    ['stock_index' => $this->_productResource->getTable('cataloginventory_stock_status')],
                    'e.' . $linkField . '= stock_index.product_id AND stock_index.stock_status = 1',
                    [self::STOCK_ATTRIBUTE => $this->prepareStockValue()]
                )->group('e.' . $linkField);
            $productCollection->setFlag('aw_onsale_collection_stock_joined', true);
        }
        return $this;
    }

    /**
     * Prepare stock value with cases for each product type
     *
     * @return \Zend_Db_Expr
     */
    private function prepareStockValue()
    {
        $sqlData = $this->attributeProcessor->prepareSqlForIndexing(self::STOCK_OPERATION);
        $connection = $this->_productResource->getConnection();
        $conditions = [];

        foreach ($sqlData as $productType => $select) {
            $case = $connection->quoteInto('?', $productType);
            $result = $select;
            $conditions[$case] = $result;
        }

        return $connection->getCaseSql('e.type_id', $conditions, 'stock_index.qty');
    }
}
