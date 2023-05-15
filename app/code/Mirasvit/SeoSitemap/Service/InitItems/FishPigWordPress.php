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

class FishPigWordPress extends \Magento\Sitemap\Model\Sitemap implements SeoSitemapInitItemInterface
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
	\Magento\Framework\Module\Manager $moduleManager,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->objectManager = $objectManager;
	$this->moduleManager = $moduleManager;
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
		if ($this->moduleManager->isEnabled('FishPig_WordPress')) {
            $result[] = new \Magento\Framework\DataObject(
            [
                'changefreq' => $this->_sitemapData->getPageChangefreq($storeId),
                'priority'   => $this->_sitemapData->getPagePriority($storeId),
                'collection' => $this->getFishPigWordpressPostsCollection($storeId),
            ]);
        }

        return $result;
    }

	private function getFishPigWordpressPostsCollection($storeId)
    {
        try {
            $emulation = $this->objectManager->create('Magento\Store\Model\App\Emulation');
            $emulation->startEnvironmentEmulation($storeId, 'frontend', true);

            $collection = $this->objectManager->get('\FishPig\WordPress\Model\ResourceModel\Post\Collection');
            $collection->addIsViewableFilter()
                ->addPostTypeFilter($this->getPostType());
            $emulation->stopEnvironmentEmulation();
        } catch (\Exception $e) {}

        $postCollection = [];
        foreach ($collection as $key => $post) {
            $postCollection[] = new \Magento\Framework\DataObject(
                [
                    'id' => $post->getId(),
                    'url'   => $post->getUrl(),
                    'title' => $post->getName(),
                    'updated_at' => $post->getPostModifiedDate(),
                ]);
        }

        return $postCollection;
    }
}
