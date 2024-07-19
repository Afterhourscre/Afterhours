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



namespace Mirasvit\SeoSitemap\Repository\Provider\Mirasvit;

use Magento\Framework\DataObject;
use Magento\Framework\ObjectManagerInterface;
use Magento\Sitemap\Helper\Data as DataHelper;
use Mirasvit\SeoSitemap\Api\Repository\ProviderInterface;

class BlogProvider implements ProviderInterface
{
    /**
     * @var DataHelper
     */
    private $dataHelper;

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * BlogProvider constructor.
     *
     * @param ObjectManagerInterface $objectManager
     * @param DataHelper             $sitemapData
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        DataHelper $sitemapData
    ) {
        $this->objectManager = $objectManager;
        $this->dataHelper    = $sitemapData;
    }

    /**
     * @return string
     */
    public function getModuleName()
    {
        return 'Mirasvit_BlogMx';
    }

    /**
     * @return bool
     */
    public function isApplicable()
    {
        return true;
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
     *
     * @return array
     */
    public function initSitemapItem($storeId)
    {
        $result = [];

        $result[] = new DataObject([
            'changefreq' => $this->dataHelper->getPageChangefreq($storeId),
            'priority'   => $this->dataHelper->getPagePriority($storeId),
            'collection' => $this->getItems($storeId),
        ]);

        return $result;
    }

    /**
     * @param int $storeId
     *
     * @return array
     */
    public function getItems($storeId)
    {
        $collection = $this->objectManager->get('Mirasvit\BlogMx\Model\ResourceModel\Post\Collection');
        $collection->addStoreFilter($storeId)
            ->addVisibilityFilter();
        $urlBuilder = class_exists('Mirasvit\BlogMx\Model\Url\UrlBuilder') ? $this->objectManager->get('Mirasvit\BlogMx\Model\Url\UrlBuilder') : '';

        $items = [];

        $catIds = [];

        foreach ($collection as $key => $post) {
            $post->setStoreId($storeId);

            $catIds = array_merge($catIds, $post->getCategoryIds());

            $items[] = new DataObject([
                'id'         => $post->getId(),
                'url'        => $urlBuilder ? $urlBuilder->getPostUrl($post): $post->getUrl(),
                'title'      => $post->getName(),
                'updated_at' => $post->getUpdatedAt(),
            ]);
        }

        $catIds = array_unique($catIds);

        if (count($catIds)) {
            $catCollection = $this->objectManager->get('Mirasvit\BlogMx\Model\ResourceModel\Category\Collection');
            $catCollection->addStoreFilter($storeId)
                ->addVisibilityFilter()
                ->addFieldToFilter('category_id', ['in' => $catIds]); // use only categories with posts

            foreach ($catCollection as $category) {
                $items[] = new DataObject([
                    'id' => $category->getId(),
                    'url' => $category->getUrl(),
                    'title' => $category->getName(),
                    'updated_at' => $category->getUpdatedAt(),
                ]);
            }
        }

        return $items;
    }
}
