<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-seo
 * @version   2.0.169
 * @copyright Copyright (C) 2020 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\SeoSitemap\Repository\Provider\Mirasvit;

use Magento\Framework\Model\Context;
use Magento\Framework\ObjectManagerInterface;
use Mirasvit\SeoSitemap\Api\Repository\ProviderInterface;

class KbProvider implements ProviderInterface
{
    private $objectManager;

    private $eventManager;

    public function __construct(
        ObjectManagerInterface $objectManager,
        Context $context
    ) {
        $this->objectManager = $objectManager;
        $this->eventManager  = $context->getEventDispatcher();
    }

    public function getModuleName()
    {
        return 'Mirasvit_Kb';
    }

    public function isApplicable()
    {
        return interface_exists('Mirasvit\Kb\Api\Data\SitemapInterface');
    }

    public function getTitle()
    {
        return __('Knowledge Base');
    }

    public function initSitemapItem($storeId)
    {
        $result = [];

        $this->eventManager->dispatch('core_register_urlrewrite');

        $kbSitemap = $this->objectManager->get('Mirasvit\Kb\Api\Data\SitemapInterface');

        $result[] = $kbSitemap->getBlogItem($storeId);

        if ($categoryItems = $kbSitemap->getCategoryItems($storeId)) {
            $result[] = $categoryItems;
        }

        if ($postItems = $kbSitemap->getPostItems($storeId)) {
            $result[] = $postItems;
        }

        return $result;
    }

    public function getItems($storeId)
    {
        return [];
    }
}
