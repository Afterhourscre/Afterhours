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


declare(strict_types=1);

namespace Mirasvit\SeoMarkup\Block\Rs;

use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\ResourceModel\Collection\AbstractCollection;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Framework\Module\Manager as ModuleManager;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template\Context;
use Mirasvit\Core\Service\SerializeService;
use Mirasvit\Seo\Api\Service\TemplateEngineServiceInterface;
use Mirasvit\SeoMarkup\Model\Config;
use Mirasvit\SeoMarkup\Model\Config\CategoryConfig;
use Mirasvit\SeoMarkup\Service\ProductRichSnippetsService;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Brand extends Category
{
    const BRAND_DEFAULT_NAME = 'BrandDefaultName';
    const BRAND_DATA         = 'm__BrandData';

    private $store;

    private $categoryConfig;

    private $productCollectionFactory;

    private $moduleManager;

    private $objectManager;

    public function __construct(
        ModuleManager                  $moduleManager,
        ObjectManagerInterface         $objectManager,
        CategoryConfig                 $categoryConfig,
        ProductCollectionFactory       $productCollectionFactory,
        TemplateEngineServiceInterface $templateEngineService,
        Registry                       $registry,
        Context                        $context,
        ProductRichSnippetsService     $productSnippetService
    ) {
        $this->categoryConfig           = $categoryConfig;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->store                    = $context->getStoreManager()->getStore();
        $this->moduleManager            = $moduleManager;
        $this->objectManager            = $objectManager;

        parent::__construct(
            $categoryConfig,
            $productCollectionFactory,
            $templateEngineService,
            $registry,
            $context,
            $productSnippetService
        );
    }

    protected function _toHtml(): string
    {
        $data = $this->getJsonData();

        if (!$data) {
            return '';
        }

        return '<script type="application/ld+json">' . SerializeService::encode($data) . '</script>';
    }

    public function getJsonData(): ?array
    {
        if (!$this->moduleManager->isEnabled('Mirasvit_Brand')) {
            return null;
        }

        $brandTitle = $this->getBrandTitle();

        if (empty($brandTitle)) {
            return null;
        }

        $collection = $this->getCollection();
        $itemList   = [];

        if ($collection) {
            $itemList = $this->getItemList($collection);
        }

        if ($collection && strripos($collection->getSelect()->__toString(), 'limit') === false) {
            $pageSize = $this->categoryConfig->getDefaultPageSize((int)$this->store->getId());
            $pageNum  = 1;

            if ($toolbar = $this->getLayout()->getBlock('product_list_toolbar')) {
                $pageSize = $toolbar->getLimit();
            }

            if ($pager = $this->getLayout()->getBlock('product_list_toolbar_pager')) {
                $pageNum = $pager->getCurrentPage();
            }

            $collection->setPageSize($pageSize)->setCurPage($pageNum);
        }

        return [
            '@context'   => Config::HTTP_SCHEMA_ORG,
            '@type'      => 'WebPage',
            'url'        => $this->_urlBuilder->escape($this->_urlBuilder->getCurrentUrl()),
            'mainEntity' => [
                '@context'        => Config::HTTP_SCHEMA_ORG,
                '@type'           => 'OfferCatalog',
                'name'            => $brandTitle,
                'url'             => $this->_urlBuilder->escape($this->_urlBuilder->getCurrentUrl()),
                'numberOfItems'   => $collection ? $collection->count() : '',
                'itemListElement' => $itemList,
            ],
        ];
    }

    protected function getCollection(): ?AbstractCollection
    {
        $productOffersType = $this->categoryConfig->getProductOffersType((int)$this->store->getId());

        if ($productOffersType === CategoryConfig::PRODUCT_OFFERS_TYPE_CURRENT_CATEGORY) {
            $collection = $this->productCollectionFactory->create();
            $collection->addAttributeToSelect('*');
            $collection->addAttributeToFilter(
                $this->getBrandAttributeCode(),
                $this->getBrandAttributeValue()
            );
            $collection->addAttributeToFilter('visibility', Visibility::VISIBILITY_BOTH);
            $collection->addAttributeToFilter('status', Status::STATUS_ENABLED);
            $collection->addFinalPrice();
            $collection->load();

            return $collection;
        } else {
            return parent::getCollection();
        }
    }

    private function getBrandTitle(): string
    {
        return (string)$this->getBrandRegistry()->getBrandPage()->getBrandTitle();
    }

    private function getBrandAttributeCode(): string
    {
        return (string)$this->getBrandRegistry()->getBrand()->getAttributeCode();
    }

    private function getBrandAttributeValue(): string
    {
        return (string)$this->getBrandRegistry()->getBrand()->getValue();
    }

    private function getBrandRegistry()
    {
        return $this->objectManager->get('Mirasvit\Brand\Registry');
    }
}
