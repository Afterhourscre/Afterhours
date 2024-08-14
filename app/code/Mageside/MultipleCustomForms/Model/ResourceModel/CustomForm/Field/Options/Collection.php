<?php
/**
 * Copyright © Mageside. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */
namespace Mageside\MultipleCustomForms\Model\ResourceModel\CustomForm\Field\Options;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        $this->storeManager = $storeManager;
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
    }

    protected function _construct()
    {
        $this->_init(
            'Mageside\MultipleCustomForms\Model\CustomForm\Field\Options',
            'Mageside\MultipleCustomForms\Model\ResourceModel\CustomForm\Field\Options'
        );
    }

    /**
     * @inheritdoc
     */
    protected function _afterLoadData()
    {
        if ($ids = $this->getAllIds()) {
            $connection = $this->getConnection();

            $storeId = $this->storeManager->getStore()->getId();
            if ($storeId != 0) {
                $stores = [$storeId, 0];
            } else {
                $stores = [0];
            }

            $labels = [];
            foreach ($stores as $store) {
                $select = $connection->select()
                    ->from(['labels' => $this->getTable('ms_cf_field_options_label')])
                    ->where('option_id IN (?)', $ids)
                    ->where('store_id = ?', $store);
                $labels = $connection->fetchAssoc($select);
                if (!empty($labels)) {
                    break;
                }
            }
            
            foreach ($this->_data as $key => $option) {
                if (!empty($labels[$option['id']])) {
                    $this->_data[$key]['label'] = $labels[$option['id']]['label'];
                }
            }
        }

        return parent::_afterLoadData();
    }
}
