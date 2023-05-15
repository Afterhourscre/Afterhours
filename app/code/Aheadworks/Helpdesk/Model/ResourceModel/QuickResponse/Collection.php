<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Helpdesk\Model\ResourceModel\QuickResponse;

use Aheadworks\Helpdesk\Model\ResourceModel\AbstractCollection;
use Aheadworks\Helpdesk\Model\QuickResponse;
use Aheadworks\Helpdesk\Model\ResourceModel\QuickResponse as ResourceQuickResponse;
use Aheadworks\Helpdesk\Api\Data\QuickResponseInterface;
use Aheadworks\Helpdesk\Model\QuickResponse\StoreValueResolver;
use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Psr\Log\LoggerInterface;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class Collection
 * @package Aheadworks\Helpdesk\Model\ResourceModel\QuickResponse
 */
class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'id';

    /**
     * @var StoreValueResolver
     */
    private $storeValueResolver;

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(QuickResponse::class, ResourceQuickResponse::class);
    }

    /**
     * Collection constructor.
     * @param EntityFactoryInterface $entityFactory
     * @param LoggerInterface $logger
     * @param FetchStrategyInterface $fetchStrategy
     * @param ManagerInterface $eventManager
     * @param StoreValueResolver $storeValueResolver
     * @param null $connection
     * @param AbstractDb|null $resource
     */
    public function __construct(
        EntityFactoryInterface $entityFactory,
        LoggerInterface $logger,
        FetchStrategyInterface $fetchStrategy,
        ManagerInterface $eventManager,
        StoreValueResolver $storeValueResolver,
        $connection = null,
        AbstractDb $resource = null
    ) {
        $this->storeValueResolver = $storeValueResolver;
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
    }

    /**
     * {@inheritdoc}
     */
    protected function _afterLoad()
    {
        $this->attachRelationTable(
            $this->getTable('aw_helpdesk_quick_response_text'),
            'id',
            'response_id',
            ['store_id', 'value'],
            QuickResponseInterface::STORE_RESPONSE_VALUES
        );

        /** @var \Magento\Framework\DataObject $item */
        foreach ($this as $item) {
            $item->setData(
                QuickResponseInterface::RESPONSE_TEXT,
                $this->storeValueResolver->getValueByStoreId(
                    $item->getStoreResponseValues(),
                    $this->storeId
                )
            );
        }

        return parent::_afterLoad();
    }
}
