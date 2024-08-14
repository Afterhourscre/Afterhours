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
 * @version   2.9.6
 * @copyright Copyright (C) 2024 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\SeoSitemap\Repository\Provider\Aheadworks;

use Magento\Framework\DataObject;
use Magento\Framework\ObjectManagerInterface;
use Mirasvit\SeoSitemap\Api\Repository\ProviderInterface;

class BlogProvider implements ProviderInterface
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    private $itemsProvider = '';

    /**
     * BlogProvider constructor.
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(
        ObjectManagerInterface $objectManager
    ) {
        $this->objectManager = $objectManager;
    }

    /**
     * @return string
     */
    public function getModuleName()
    {
        return 'Aheadworks_Blog';
    }

    /**
     * @return bool
     */
    public function isApplicable()
    {
        if (class_exists('Aheadworks\Blog\Model\Sitemap\ItemsProvider')) {
            $this->itemsProvider = 'Aheadworks\Blog\Model\Sitemap\ItemsProvider';
        } elseif (class_exists('Aheadworks\Blog\Model\Sitemap\ItemsProviderComposite')) {
            $this->itemsProvider = 'Aheadworks\Blog\Model\Sitemap\ItemsProviderComposite';
        }

        return !!$this->itemsProvider;
    }

    /**
     * @return \Magento\Framework\Phrase|string
     */
    public function getTitle()
    {
        return __('Blog');
    }

    /**
     * @param int $storeId
     * @return array
     */
    public function initSitemapItem($storeId)
    {
        $result = [];

        if (strpos($this->itemsProvider, 'ItemsProviderComposite') !== false) {
            $sitemapHelper = $this->objectManager->get($this->itemsProvider);

            return $sitemapHelper->getItems($storeId);
        }

        $sitemapHelper = $this->objectManager->get($this->itemsProvider);

        $result[] = $sitemapHelper->getBlogItem($storeId);
        $result[] = $sitemapHelper->getCategoryItems($storeId);
        $result[] = $sitemapHelper->getPostItems($storeId);

        return $result;
    }

    /**
     * @param int $storeId
     * @return array
     */
    public function getItems($storeId)
    {
        $configProvider            = $this->objectManager->create('Aheadworks\Blog\Model\Config');
        $urlBuilder                = $this->objectManager->create('Magento\Framework\UrlInterface');
        $urlHelper                 = $this->objectManager->create('Aheadworks\Blog\Model\Url');
        $categoryCollectionFactory = $this->objectManager->create('Aheadworks\Blog\Model\ResourceModel\Category\CollectionFactory');
        $postCollectionFactory     = $this->objectManager->create('Aheadworks\Blog\Model\ResourceModel\Post\CollectionFactory');

        $items = [];
        $home  = $configProvider->getRouteToBlog($storeId);

        if (!empty($home)) {
            $items['home'] = new DataObject([
                'title' => 'Blog Home',
                'url'  => $urlBuilder->getUrl($home),
            ]);
        }

        $categoryCollection = $categoryCollectionFactory->create()
            ->addFieldToFilter('status', ['eq' => '1'])
            ->addStoreFilter($storeId);

        foreach ($categoryCollection->getItems() as $category) {
            $items['cat' . $category->getId()] = new DataObject([
                'title' => $category->getName(),
                'url'  => $urlBuilder->getUrl($urlHelper->getCategoryRoute($category)),
            ]);
        }

        $postCollection = $postCollectionFactory->create()
            ->addFieldToFilter('status', ['eq' => 'publication'])
            ->addStoreFilter($storeId);

        foreach ($postCollection->getItems() as $post) {
            $items['post' . $post->getId()] = new DataObject([
                'title' => $post->getTitle(),
                'url'  => $urlBuilder->getUrl($urlHelper->getPostRoute($post)),
            ]);
        }

        return $items;
    }
}
