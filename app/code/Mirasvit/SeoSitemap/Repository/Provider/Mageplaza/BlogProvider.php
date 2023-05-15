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



namespace Mirasvit\SeoSitemap\Repository\Provider\Mageplaza;

use Magento\Framework\DataObject;
use Magento\Framework\ObjectManagerInterface;
use Magento\Sitemap\Helper\Data as DataHelper;
use Mirasvit\SeoSitemap\Api\Repository\ProviderInterface;

class BlogProvider implements ProviderInterface
{
    private $dataHelper;

    private $objectManager;

    public function __construct(
        ObjectManagerInterface $objectManager,
        DataHelper $sitemapData
    ) {
        $this->objectManager = $objectManager;
        $this->dataHelper    = $sitemapData;
    }

    public function getModuleName()
    {
        return 'Mageplaza_Blog';
    }

    public function isApplicable()
    {
        return true;
    }

    public function getTitle()
    {
        return __('Blog');
    }

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

    public function getItems($storeId)
    {
        /** @var \Mageplaza\Blog\Helper\Data $helper */
        $helper     = $this->objectManager->get('Mageplaza\Blog\Helper\Data');
        $collection = $helper->getPostCollection(null, null, $storeId);
        $route = $helper->getRoute();
        $items = [];

        foreach ($collection as $key => $post) {
            $items[] = new DataObject([
                'id'         => $post->getId(),
                'url'        => $route . '/' . $post->getUrlKey(),
                'title'      => $post->getName(),
                'updated_at' => $post->getUpdatedAt(),
            ]);
        }

        return $items;
    }
}
