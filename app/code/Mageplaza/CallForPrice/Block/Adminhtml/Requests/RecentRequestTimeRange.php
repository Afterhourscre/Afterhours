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
use Magento\Framework\Stdlib\DateTime\DateTime;
use Mageplaza\CallForPrice\Helper\Data as HelperData;
use Mageplaza\CallForPrice\Model\RequestsFactory;
use Mageplaza\CallForPrice\Model\ResourceModel\Requests\CollectionFactory as RequestsCollection;

/**
 * Class RecentRequestTimeRange
 * @package Mageplaza\CallForPrice\Block\Adminhtml\Requests
 */
class RecentRequestTimeRange extends Grid
{
    /**
     * @var string template
     */
    protected $_template = 'dashboard/grid.phtml';

    /**
     * @var RequestsCollection
     */
    protected $_collectionFactory;

    /**
     * @var DateTime
     */
    protected $_requestsFactory;

    /**
     * @var HelperData
     */
    protected $_helperData;

    /**
     * RecentRequestTimeRange constructor.
     * @param Context $context
     * @param RequestsCollection $collectionFactory
     * @param Data $backendHelper
     * @param RequestsFactory $requestsFactory
     * @param HelperData $helperData
     * @param array $data
     */
    public function __construct(
        Context $context,
        RequestsCollection $collectionFactory,
        Data $backendHelper,
        RequestsFactory $requestsFactory,
        HelperData $helperData,
        array $data = []
    )
    {
        $this->_collectionFactory = $collectionFactory;
        $this->_requestsFactory   = $requestsFactory;
        $this->_helperData        = $helperData;

        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();

        $this->setId('recentRequestedProducts');
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareCollection()
    {
        $dateRange = $this->_helperData->getDateRange();

        $collection = $this->_collectionFactory->create();
        $collection->addFieldToFilter('created_at', ['lteq' => $dateRange[1]])
            ->addFieldToFilter('created_at', ['gteq' => $dateRange[0]]);

        $collection->getSelect()
            ->order('created_at desc');

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareColumns()
    {
        $this->addColumn('name', ['header' => __('Name'), 'sortable' => false, 'index' => 'name']);

        $this->addColumn('item_product', [
            'header'           => __('Product'),
            'sortable'         => false,
            'index'            => 'item_product',
            'header_css_class' => 'col-views',
            'column_css_class' => 'col-views'
        ]);

        $this->addColumn('created_at', [
            'header'   => __('Created At'),
            'sortable' => false,
            'index'    => 'created_at',
            'timezone' => false,
            'type'     => 'datetime',
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
        $params = ['request_id' => $row->getRequestId()];
        if ($this->getRequest()->getParam('store')) {
            $params['store'] = $this->getRequest()->getParam('store');
        }

        return $this->getUrl('mpcallforprice/requests/edit', $params);
    }

    /**
     * @return int
     */
    public function getNoRequestByTimeRange()
    {
        $dateRange = $this->_helperData->getDateRange();
        $fromdate  = $dateRange[0];
        $todate    = $dateRange[1];

        $collectionFilterBYTimeRange = $this->_requestsFactory->create()->getCollection()
            ->addFieldToFilter('created_at', ['lteq' => $todate])
            ->addFieldToFilter('created_at', ['gteq' => $fromdate]);

        return count($collectionFilterBYTimeRange);
    }

    /**
     * @return int
     */
    public function getNoRequestByTimeRangeCompare()
    {
        $dateRange = $this->_helperData->getDateRange();
        $fromdate  = $dateRange[2];
        $todate    = $dateRange[3];

        $collectionFilterBYTimeRangeComepare = $this->_requestsFactory->create()->getCollection()
            ->addFieldToFilter('created_at', ['lteq' => $todate])
            ->addFieldToFilter('created_at', ['gteq' => $fromdate]);

        return count($collectionFilterBYTimeRangeComepare);
    }

    /**
     * @return float
     */
    public function getNoRequestByTimeRangePercentUnit()
    {
        $noRequest                      = $this->getNoRequestByTimeRange();
        $getNoRequestByTimeRangeCompare = $this->getNoRequestByTimeRangeCompare();

        if ($noRequest > 0 && $getNoRequestByTimeRangeCompare > 0) {
            $percentUnit = round(($noRequest - $getNoRequestByTimeRangeCompare) / $getNoRequestByTimeRangeCompare * 100, 2);
        } else {
            $percentUnit = 0;
        }

        return abs($percentUnit);
    }
}
