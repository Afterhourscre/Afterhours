<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the mageplaza.com license that is
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

namespace Mageplaza\CallForPrice\Block\Adminhtml\Requests;

use Magento\Backend\Block\Dashboard\Grid;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Helper\Data;
use Mageplaza\CallForPrice\Model\ResourceModel\Requests\Collection as RequestsCollection;

/**
 * Class TopRequestedProducts
 * @package Mageplaza\CallForPrice\Block\Adminhtml\Requests
 */
class TopRequestedProducts extends Grid
{
    /**
     * @var string
     */
    protected $_template = 'dashboard/toprequest.phtml';

    /**
     * @var RequestsCollection
     */
    protected $_collectionFactory;

    /**
     * TopRequestedProducts constructor.
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param RequestsCollection                      $collectionFactory
     * @param Data                                    $backendHelper
     * @param array                                   $data
     */
    public function __construct(
        Context $context,
        RequestsCollection $collectionFactory,
        Data $backendHelper,
        array $data = []
    )
    {
        $this->_collectionFactory = $collectionFactory;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('topRequestedProductsGrid');
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareCollection()
    {
        $collection = $this->_collectionFactory;

        $collection->getSelect()->reset()->from(
            ['callforprice_requests' => $collection->getTable('mageplaza_callforprice_requests')],
            ['product_id', 'item_product', 'rank_request' => 'COUNT(callforprice_requests.product_id)']
        )
            ->group('callforprice_requests.product_id')
            ->order('rank_request desc')
            ->having('COUNT(callforprice_requests.product_id) > ?', 0);


        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareColumns()
    {
        $this->addColumn('item_product', ['header' => __('Product'), 'sortable' => false, 'index' => 'item_product']);

        $this->addColumn('rank_request', [
            'header'           => __('Requested'),
            'sortable'         => false,
            'index'            => 'rank_request',
            'header_css_class' => 'col-views',
            'column_css_class' => 'col-views'
        ]);

        $this->setFilterVisibility(false);
        $this->setPagerVisibility(false);

        return parent::_prepareColumns();
    }

    /**
     * {@inheritdoc}
     */
    public function getRowUrl($row)
    {
        $params = ['id' => $row->getProductId()];
        if ($this->getRequest()->getParam('store')) {
            $params['store'] = $this->getRequest()->getParam('store');
        }

        return $this->getUrl('catalog/product/edit', $params);
    }
}
