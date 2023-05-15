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
 * @version   2.0.154
 * @copyright Copyright (C) 2019 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\SeoSitemap\Service\InitItems;

use Mirasvit\SeoSitemap\Api\Service\InitItems\SeoSitemapInitItemInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Model\Context;

class MirasvitKb extends \Magento\Sitemap\Model\Sitemap implements SeoSitemapInitItemInterface
{
    public function __construct(
        ObjectManagerInterface $objectManager,
        Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Escaper $escaper,
        \Magento\Sitemap\Helper\Data $sitemapData,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Sitemap\Model\ResourceModel\Catalog\CategoryFactory $categoryFactory,
        \Magento\Sitemap\Model\ResourceModel\Catalog\ProductFactory $productFactory,
        \Magento\Sitemap\Model\ResourceModel\Cms\PageFactory $cmsFactory,
        \Magento\Framework\Stdlib\DateTime\DateTime $modelDate,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\Stdlib\DateTime $dateTime,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->objectManager    = $objectManager;
        $this->eventManager     = $context->getEventDispatcher();
        parent::__construct($context,
            $registry,
            $escaper,
            $sitemapData,
            $filesystem,
            $categoryFactory,
            $productFactory,
            $cmsFactory,
            $modelDate,
            $storeManager,
            $request,
            $dateTime,
            $resource,
            $resourceCollection,
            $data
        );
    }

    public function initSitemapItem($storeId)
    {
    	$result = [];
		if (interface_exists('\Mirasvit\Kb\Api\Data\SitemapInterface')) {
            $this->eventManager->dispatch("core_register_urlrewrite");
            $kbSitemap = $this->objectManager->get('\Mirasvit\Kb\Api\Data\SitemapInterface');

            $result[] = $kbSitemap->getBlogItem($storeId);

            if ($categoryItems = $kbSitemap->getCategoryItems($storeId)) {
                $result[] = $categoryItems;
            }

            if ($postItems = $kbSitemap->getPostItems($storeId)) {
                $result[] = $postItems;
            }
        }

        return $result;
    }
}
