<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */


namespace Aheadworks\Faq\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Data\Tree\Node;
use Magento\Framework\Event\ObserverInterface;
use Aheadworks\Faq\Model\Url;
use Aheadworks\Faq\Model\Config;

/**
 * Class FAQ NavigationMenu
 */
class NavigationMenu implements ObserverInterface
{
    /**
     * @var Url
     */
    private $url;

    /**
     * @var Config
     */
    private $config;

    /**
     * @param Url $url
     * @param Config $config
     */
    public function __construct(Url $url, Config $config)
    {
        $this->url = $url;
        $this->config = $config;
    }

    /**
     * @param EventObserver $observer
     * @return $this
     */
    public function execute(EventObserver $observer)
    {
        if ($this->config->isNavigationMenuLinkEnabled() && !$this->config->isDisabledFaqForCurrentCustomer()) {
            /**
             * @var Node $menu
             */
            $menu = $observer->getMenu();
            $block = $observer->getBlock();

            $tree = $menu->getTree();
            $data = [
                'name' => $this->config->getFaqName(),
                'id' => 'aw_faq',
                'url' => $this->url->getFaqHomeUrl(),
                'is_active' => ($block->getRequest()->getModuleName() == 'faq'),
            ];
            $node = new Node($data, 'id', $tree, $menu);
            $menu->addChild($node);
        }

        return $this;
    }
}
