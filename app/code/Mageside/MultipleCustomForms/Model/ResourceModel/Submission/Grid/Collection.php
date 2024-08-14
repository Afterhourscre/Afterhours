<?php
/**
 * Copyright © Mageside. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */
namespace Mageside\MultipleCustomForms\Model\ResourceModel\Submission\Grid;

use Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface as FetchStrategy;
use Magento\Framework\Data\Collection\EntityFactoryInterface as EntityFactory;
use Psr\Log\LoggerInterface as Logger;

class Collection extends SearchResult
{
    /**
     * CustomForm constructor.
     * @param EntityFactory $entityFactory
     * @param Logger $logger
     * @param FetchStrategy $fetchStrategy
     * @param EventManager $eventManager
     * @param string $mainTable
     * @param string $resourceModel
     */
    public function __construct(
        EntityFactory $entityFactory,
        Logger $logger,
        FetchStrategy $fetchStrategy,
        EventManager $eventManager,
        $mainTable = 'ms_cf_submission',
        $resourceModel = 'Mageside\MultipleCustomForms\Model\ResourceModel\Submission'
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $mainTable, $resourceModel);
    }

    /**
     * @return $this
     */
    protected function _initSelect()
    {
        $this->getSelect()
            ->from(['main_table' => $this->getMainTable()])
            ->joinLeft(
                ['cf' => $this->getTable('ms_custom_form_settings')],
                "main_table.form_id = cf.form_id AND cf.store_id = '0'",
                ['name' => 'cf.name']
            );

        return $this;
    }

    /**
     * @param array|string $field
     * @param null $condition
     * @return $this
     */
    public function addFieldToFilter($field, $condition = null)
    {
        switch ($field) {
            case 'main_table.form_id':
            case 'form_id':
                $field = 'main_table.form_id';
                break;
            case 'id':
                $field = 'main_table.id';
                break;
            case 'created_at':
                $field = 'main_table.created_at';
                break;
            default:
                $field = $field . '.value';
        }

        return parent::addFieldToFilter($field, $condition);
    }
}
