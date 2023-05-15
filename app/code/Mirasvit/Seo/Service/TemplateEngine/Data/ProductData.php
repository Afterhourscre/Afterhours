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



namespace Mirasvit\Seo\Service\TemplateEngine\Data;

use Magento\Catalog\Helper\Data as CatalogHelper;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Framework\Pricing\Helper\Data as PricingHelper;
use Magento\Framework\Registry;
use Magento\Store\Model\StoreManagerInterface;

class ProductData extends AbstractData
{
    private $registry;

    private $catalogHelper;

    private $categoryFactory;

    private $storeManager;

    private $pricingHelper;

    public function __construct(
        Registry $registry,
        CatalogHelper $catalogHelper,
        CategoryFactory $categoryFactory,
        StoreManagerInterface $storeManager,
        PricingHelper $pricingHelper
    ) {
        $this->registry        = $registry;
        $this->catalogHelper   = $catalogHelper;
        $this->categoryFactory = $categoryFactory;
        $this->storeManager    = $storeManager;
        $this->pricingHelper   = $pricingHelper;

        parent::__construct();
    }

    public function getTitle()
    {
        return __('Product Data');
    }

    public function getVariables()
    {
        return [
            'name',
            'url',
            'page_title',
            'parent_name',
            'parent_url',
            'parent_parent_name',
            'category_name',
        ];
    }

    public function getValue($attribute, $additionalData = [])
    {
        /** @var \Magento\Catalog\Model\Product $product */
        $product = isset($additionalData['product'])
            ? $additionalData['product']
            : $this->registry->registry('current_product');

        if (!$product) {
            return false;
        }

        switch ($attribute) {
            case 'price':
                $price = null;
                if ($product->getTypeId() === 'simple') {
                    //other products types include tax by default
                    $price = $this->catalogHelper->getTaxPrice($product, $product->getFinalPrice());
                } else {
                    $price = $product->getFinalPrice();
                }

                return $this->pricingHelper->currency($price, false, false);

            case 'url':
                return $product->getProductUrl();

            case 'name':
                return $product->getName();

            case 'sku':
                return $product->getSku();

            case 'category_name':
                if ($category = $this->registry->registry('current_category')) {
                    return $category->getName();
                }

                $categoryIds = $product->getCategoryIds();
                $categoryIds = array_reverse($categoryIds);

                if (isset($categoryIds[0])) {
                    return $this->categoryFactory->create()
                        ->setStoreId($this->storeManager->getStore()->getId())
                        ->load($categoryIds[0])
                        ->getName();
                }

                return false;
        }

        if ($attributes = $product->getAttributes()) {
            foreach ($attributes as $attr) {
                if (isset($additionalData['store'])) {
                    // required for use correct attribute labels (color Black FR) during url-generation
                    $attr->setStoreId($additionalData['store']->getId());
                }

                if ($attr->getAttributeCode() === $attribute) {
                    return $attr->getFrontend()->getValue($product);
                }
            }
        }

        return $product->getDataUsingMethod($attribute);
    }
}
