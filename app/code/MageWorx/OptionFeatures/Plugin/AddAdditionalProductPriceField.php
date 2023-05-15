<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\OptionFeatures\Plugin;

use \Magento\Catalog\Block\Product\View;
use MageWorx\OptionFeatures\Helper\Data as Helper;
use Magento\Catalog\Api\Data\ProductInterface;

class AddAdditionalProductPriceField
{
    /**
     * @var Helper
     */
    protected $helper;

    /**
     * @param Helper $helper
     */
    public function __construct(
        Helper $helper
    ) {
        $this->helper = $helper;
    }

    /**
     * Add additional product price field
     *
     * @param View $subject
     * @param string $template
     * @return string
     */
    public function beforeSetTemplate($subject, $template)
    {
        $blockName = $subject->getNameInLayout();
        if ($blockName === 'product.info.addtocart.additional'
            && $subject->getProduct()
            && $this->showAdditionalField($subject->getProduct())
        ) {
            $template = "MageWorx_OptionFeatures::catalog/product/addtocart.phtml";
        }
        return $template;
    }

    /**
     * Show additional field
     *
     * @param ProductInterface $product
     * @return bool
     */
    protected function showAdditionalField($product)
    {
        return $this->isNotBundle($product)
            && $this->isNotConfigurable($product)
            && $this->helper->isEnabledAdditionalProductPriceField($product->getStoreId())
            && $this->canShowAdditionalProductPriceField($product)
            && $product->getTypeInstance()->hasOptions($product);
    }

    /**
     * Check if product is configurable
     *
     * @param ProductInterface $product
     * @return bool
     */
    protected function isNotConfigurable($product)
    {
        return $product->getTypeId() !== 'configurable';
    }

    /**
     * Check if product is bundle
     *
     * @param ProductInterface $product
     * @return bool
     */
    protected function isNotBundle($product)
    {
        return $product->getTypeId() !== 'bundle';
    }

    /**
     * Check if additional product field should be shown for a product
     *
     * @param ProductInterface $product
     * @return bool
     */
    protected function canShowAdditionalProductPriceField($product)
    {
        return !$product->getHideAdditionalProductPrice();
    }
}
