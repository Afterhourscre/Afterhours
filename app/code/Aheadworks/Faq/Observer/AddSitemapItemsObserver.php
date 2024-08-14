<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Faq\Observer;

use Aheadworks\Faq\Model\Sitemap;
use Aheadworks\Faq\Model\Sitemap\ItemsProvider;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class AddSitemapItemsObserver
 * @package Aheadworks\Faq\Observer
 */
class AddSitemapItemsObserver implements ObserverInterface
{
    /**
     * @var ItemsProvider
     */
    private $itemsProvider;

    /**
     * @param ItemsProvider $itemsProvider
     */
    public function __construct(ItemsProvider $itemsProvider)
    {
        $this->itemsProvider = $itemsProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(Observer $observer)
    {
        $event = $observer->getEvent();
        /** @var Sitemap $sitemap */
        $sitemap = $event->getObject();
        $storeId = $sitemap->getStoreId();
        $sitemap
            ->appendSitemapItem($this->itemsProvider->getFaqHomePageItem($storeId))
            ->appendSitemapItem($this->itemsProvider->getCategoryItems($storeId))
            ->appendSitemapItem($this->itemsProvider->getArticleItems($storeId));
    }
}
