<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_CallForPrice
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\CallForPrice\Block\Adminhtml\Rules\Edit;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Grid\Extended;
use Magento\Backend\Helper\Data;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Mageplaza\CallForPrice\Model\RulesFactory;

/**
 * Class Grid
 * @package Mageplaza\CallForPrice\Block\Adminhtml\Rules\Edit
 */
class Grid extends Extended
{
    /**
     * @var CollectionFactory
     */
    protected $_productCollectionFactory;

    /**
     * @var RuleFactory
     */
    protected $_ruleFactory;

    /**
     * Grid constructor.
     *
     * @param Context $context
     * @param Data $backendHelper
     * @param CollectionFactory $productCollectionFactory
     * @param RulesFactory $ruleFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        Data $backendHelper,
        CollectionFactory $productCollectionFactory,
        RulesFactory $ruleFactory,
        array $data = []
    )
    {
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_ruleFactory              = $ruleFactory;

        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * _construct
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();

        $this->setId('productsGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(false);
        $this->setUseAjax(true);
    }

    /**
     * prepare collection
     */
    protected function _prepareCollection()
    {
        $collection = $this->_productCollectionFactory->create()->addStoreFilter()
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('sku')
            ->addAttributeToSelect('price');
        if (count($this->_getSelectedProducts())) {
            $collection->addIdFilter($this->_getSelectedProducts());
        } else {
            $collection->addIdFilter([0]);
        }
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * @return Extended
     * @throws \Exception
     */
    protected function _prepareColumns()
    {
        $this->addColumn('entity_id', [
            'header'           => __('Product ID'),
            'type'             => 'number',
            'index'            => 'entity_id',
            'header_css_class' => 'col-id',
            'column_css_class' => 'col-id',
        ]);
        $this->addColumn('name', [
            'header' => __('Name'),
            'index'  => 'name',
            'width'  => '50px',
        ]);
        $this->addColumn('sku', [
            'header' => __('Sku'),
            'index'  => 'sku',
            'width'  => '50px',
        ]);
        $this->addColumn('price', [
            'header' => __('Price'),
            'type'   => 'currency',
            'index'  => 'price',
            'width'  => '50px',
        ]);

        return parent::_prepareColumns();
    }

    /**
     * Get Grid Url for product list
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('mpcallforprice/grid/productlist', ['_current' => true]);
    }

    /**
     * @param  object $row
     *
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl(
            'catalog/product/edit',
            ['store' => $this->getRequest()->getParam('store'), 'id' => $row->getId()]
        );
    }

    /**
     * @return mixed
     */
    protected function _getSelectedProducts()
    {
        $ruleId = $this->getRequest()->getParam('rule_id');
        $rule   = $this->_ruleFactory->create()->load($ruleId);
        if (!$rule) {
            return false;
        }

        $productIds = $rule->getMatchingProductIds();

        return $productIds;
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return true;
    }
}

