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



namespace Mirasvit\SeoSitemap\Repository\Provider\Magento;

use Magento\Framework\DataObject;
use Magento\Sitemap\Helper\Data as DataHelper;
use Magento\Sitemap\Model\ResourceModel\Catalog\CategoryFactory;
use Mirasvit\SeoSitemap\Api\Repository\ProviderInterface;

class CategoryProvider implements ProviderInterface
{
    private $dataHelper;

    private $categoryFactory;

    public function __construct(
        DataHelper $dataHelper,
        CategoryFactory $categoryFactory
    ) {
        $this->dataHelper      = $dataHelper;
        $this->categoryFactory = $categoryFactory;
    }

    public function getModuleName()
    {
        return "Magento_Catalog";
    }

    public function isApplicable()
    {
        return true;
    }

    public function getTitle()
    {
        return __('Categories');
    }

    public function initSitemapItem($storeId)
    {
        return new DataObject([
            'changefreq' => $this->dataHelper->getCategoryChangefreq($storeId),
            'priority'   => $this->dataHelper->getCategoryPriority($storeId),
            'collection' => $this->categoryFactory->create()->getCollection($storeId),
        ]);
    }

    public function getItems($storeId)
    {
        return [];
    }
}
