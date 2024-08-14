<?php
/**
 * Copyright © Mageside. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */
namespace Mageside\MultipleCustomForms\Model\ResourceModel\CustomForm\Fieldset;

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
            'Mageside\MultipleCustomForms\Model\CustomForm\Fieldset',
            'Mageside\MultipleCustomForms\Model\ResourceModel\CustomForm\Fieldset'
        );
    }

    protected function _afterLoad()
    {
        $savedSettings = $this->loadAdditionalSettings();
        if (!empty($savedSettings)) {
            foreach ($this->getItems() as $item) {
                if (array_key_exists($item->getId(), $savedSettings)) {
                    $item->addData($savedSettings[$item->getId()]);
                }
            }
        }

        parent::_afterLoad();
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function loadAdditionalSettings()
    {
        $settings = [];
        $ids = $this->getAllIds();


        if (!empty($ids)) {
            $connection = $this->getConnection();

            $storeId = $this->storeManager->getStore()->getId();
            $stores = [0];
            if ($storeId != 0) {
                $stores = [0, $storeId];
            }

            foreach ($stores as $storeId) {
                $select = $connection->select()
                    ->from($this->getTable('ms_cf_fieldset_settings'))
                    ->where('fieldset_id in (?)', $ids)
                    ->where('store_id = ?', $storeId);

                $savedSettings = $connection->fetchAll($select);

                if (!empty($savedSettings)) {
                    foreach ($savedSettings as $setting) {
                        $settings[$setting['fieldset_id']] = $setting;
                    }
                }
            }
        }

        return $settings;
    }

    /**
     * Add order collection by position
     *
     * @return $this
     */
    public function addOrderByPosition()
    {
        $this->addOrder('position', \Magento\Framework\Data\Collection::SORT_ORDER_ASC);

        return $this;
    }
}
