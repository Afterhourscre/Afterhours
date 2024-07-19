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

class BrandProvider implements ProviderInterface
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
        return 'Mirasvit_Brand';
    }

    public function isApplicable()
    {
        return true;
    }

    public function getTitle()
    {
        return __('Brand');
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
        $repository = $this->objectManager->get('Mirasvit\Brand\Repository\BrandRepository');
        $collection = $this->objectManager->create('Mirasvit\Brand\Model\ResourceModel\BrandPage\Collection');
        $brandUrlService = $this->objectManager->create('Mirasvit\Brand\Service\BrandUrlService');
        $collection->addStoreFilter($storeId);
        $collection->addEnableFilter();

        $items = [];

        foreach ($collection as $key => $brandPage) {
            $brandModel = $repository->get($brandPage->getAttributeOptionId());

            if (!$brandModel) {
                continue; // in case attribute for brand was changed
            }

            $items[] = new DataObject([
                'id'         => $brandPage->getBrandPageId(),
                'url'        => $brandUrlService->getBrandUrl($brandModel, $storeId),
                'title'      => $brandPage->getBrandTitle(),
            ]);
        }
        return $items;
    }
}
