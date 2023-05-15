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
use Magento\Sitemap\Model\ResourceModel\Catalog\ProductFactory;
use Mirasvit\SeoSitemap\Api\Repository\ProviderInterface;

class ProductProvider implements ProviderInterface
{
    private $dataHelper;

    private $productFactory;

    public function __construct(
        DataHelper $sitemapData,
        ProductFactory $productFactory
    ) {
        $this->dataHelper     = $sitemapData;
        $this->productFactory = $productFactory;
    }

    public function getModuleName()
    {
        return 'Magento_Catalog';
    }

    public function getTitle()
    {
        return __('Products');
    }

    public function isApplicable()
    {
        return true;
    }

    public function initSitemapItem($storeId)
    {
        return new DataObject([
            'changefreq' => $this->dataHelper->getProductChangefreq($storeId),
            'priority'   => $this->dataHelper->getProductPriority($storeId),
            'collection' => $this->productFactory->create()->getCollection($storeId),
        ]);
    }

    public function getItems($storeId)
    {
        return [];
    }
}
