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



namespace Mirasvit\SeoSitemap\Block\Map;

use Magento\Framework\Module\Manager as ModuleManager;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\View\Element\Template\Context;
class AwBlog extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Framework\Module\Manager
     */
    private $objectManager;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    private $moduleManager;

    /**
     * @var \Magento\Framework\View\Element\Template\Context
     */
    private $context;

    public function __construct(
        ModuleManager $moduleManager,
        ObjectManagerInterface $objectManager,
        Context $context
    ){
        $this->objectManager    = $objectManager;
        $this->moduleManager    = $moduleManager;
        $this->context          = $context;
    }

    public function getTitle()
    {
        return __('Blog');
    }

    public function getAwBlogData()
    {
        if (!$this->canUseAwBlog()) {            
            return false;
        }

        $sitemapHelper             = $this->objectManager->create('\Aheadworks\Blog\Helper\Sitemap');
        $urlHelper                 = $this->objectManager->create('Aheadworks\Blog\Helper\Url');
        $categoryCollectionFactory = $this->objectManager->create('\Aheadworks\Blog\Model\ResourceModel\Category\CollectionFactory');
        $postCollectionFactory     = $this->objectManager->create('\Aheadworks\Blog\Model\ResourceModel\Post\CollectionFactory');

        $storeId = $this->context->getStoreManager()->getStore()->getId();
        $items   = [];
        $home    = $sitemapHelper->getBlogItem($storeId)->getCollection();

        if (isset($home[0]) && is_object($home[0])) {
            $items['home'] = new \Magento\Framework\DataObject(
                [
                    'name' => 'Blog Home',
                    'url'  => $home[0]->getUrl(),
                ]
            );
        }

        $categoryCollection = $categoryCollectionFactory->create()
            ->addEnabledFilter()
            ->addStoreFilter($storeId);
        foreach ($categoryCollection as $category) {
            $items['cat' . $category->getId()] = new \Magento\Framework\DataObject(
                [
                    'name' => $category->getName(),
                    'url'  => $urlHelper->getCategoryRoute($category),
                ]
            );
        }

        $postCollection = $postCollectionFactory->create()
            ->addPublishedFilter()
            ->addStoreFilter($storeId);

        foreach ($postCollection as $post) {
            $items['post' . $post->getId()] = new \Magento\Framework\DataObject(
                [
                    'name' => $post->getTitle(),
                    'url'  => $urlHelper->getPostRoute($post),
                ]
            );
        }

        return $items;
    }

    /**
     * @return bool
     */
    public function canUseAwBlog()
    {
        if ($this->moduleManager->isEnabled('Aheadworks_Blog') && (
            class_exists('\Aheadworks\Blog\Helper\Sitemap')
            && class_exists('Aheadworks\Blog\Helper\Url')
            && class_exists('\Aheadworks\Blog\Model\ResourceModel\Category\CollectionFactory')
            && class_exists('\Aheadworks\Blog\Model\ResourceModel\Post\CollectionFactory'))) {

            return true;
        }

        return false;
    }
}
