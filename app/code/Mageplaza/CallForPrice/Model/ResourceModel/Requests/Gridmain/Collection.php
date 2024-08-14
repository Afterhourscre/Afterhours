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

namespace Mageplaza\CallForPrice\Model\ResourceModel\Requests\Gridmain;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface as FetchStrategy;
use Magento\Framework\Data\Collection\EntityFactoryInterface as EntityFactory;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult;
use Mageplaza\CallForPrice\Helper\Data as HelperData;
use Psr\Log\LoggerInterface as Logger;

/**
 * Class Collection
 * @package Mageplaza\CallForPrice\Model\ResourceModel\Requests\Grid
 */
class Collection extends SearchResult
{
    /**
     * Request object
     *
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $_request;

    /**
     * @var HelperData
     */
    protected $helperData;

    /**
     * Collection constructor.
     *
     * @param EntityFactory $entityFactory
     * @param Logger $logger
     * @param FetchStrategy $fetchStrategy
     * @param EventManager $eventManager
     * @param RequestInterface $request
     * @param HelperData $helperData
     * @param string $mainTable
     * @param string $resourceModel
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function __construct(
        EntityFactory $entityFactory,
        Logger $logger,
        FetchStrategy $fetchStrategy,
        EventManager $eventManager,
        RequestInterface $request,
        HelperData $helperData,
        $mainTable = 'mageplaza_callforprice_requests',
        $resourceModel = '\Mageplaza\CallForPrice\Model\ResourceModel\Requests'
    )
    {
        $this->_request   = $request;
        $this->helperData = $helperData;
        // die('123');
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $mainTable, $resourceModel);
    }

    /**
     * @return $this
     */
    protected function _initSelect()
    {
       
        parent::_initSelect();
        $fields = ['status', 'created_at'];
        foreach ($fields as $field) {
            $this->addFilterToMap($field, 'main_table.' . $field);
        }

        $fullActionName = $this->helperData->getFullActionName();
        if ($fullActionName != 'mpcallforprice_requests_massDelete' && $fullActionName != 'mpcallforprice_requests_massStatus') {
            $dateRange = $this->getDateRange();
            $fromdate  = $dateRange[0];
            $todate    = $dateRange[1];

            if (isset($dateRange[0]) && $dateRange[0] != null && isset($dateRange[1]) && $dateRange[1] != null) {
                $this->addFieldToFilter('created_at', ['lteq' => $todate])
                    ->addFieldToFilter('created_at', ['gteq' => $fromdate]);
            }
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getDateRange()
    {
        $dateRange = $this->helperData->getDateRange();
        if ($this->_request->getParam('mpFilter') !== null) {
            $mpFilter_Param     = $this->_request->getParam('mpFilter');
            $mpFilter_startDate = $mpFilter_Param['startDate'];
            $mpFilter_endDate   = $mpFilter_Param['endDate'];

            list($mpFilter_startDate, $mpFilter_endDate) = $this->helperData->getDateTimeRangeFormat($mpFilter_startDate, $mpFilter_endDate);
            $dateRange[0] = $mpFilter_startDate;
            $dateRange[1] = $mpFilter_endDate;
        } else {
            if ($startDate = $this->_request->getParam('startDate')) {
                $dateRange[0] = $startDate;
            }
            if ($endDate = $this->_request->getParam('endDate')) {
                $dateRange[1] = $endDate;
            }
        }

        return $dateRange;
    }
}
