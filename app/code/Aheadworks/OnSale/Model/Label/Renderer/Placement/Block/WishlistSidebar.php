<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Model\Label\Renderer\Placement\Block;

use Magento\Wishlist\Helper\Data as WishlistHelper;
use Aheadworks\OnSale\Block\Label\Renderer as LabelRenderer;
use Aheadworks\OnSale\Block\Label\RendererFactory as LabelRendererFactory;
use Magento\Catalog\Model\Product;
use Aheadworks\OnSale\Model\Source\Label\Renderer\Placement;
use Aheadworks\OnSale\Model\Source\Label\Renderer\Size;

/**
 * Class WishlistSidebar
 *
 * @package Aheadworks\OnSale\Model\Label\Renderer\Placement\Block
 */
class WishlistSidebar
{
    /**
     * KO template for wishlist image including on sale label
     */
    const KO_TEMPLATE_FOR_WISHLIST_IMAGE = 'Aheadworks_OnSale/view/product/wishlist/image-with-label';

    /**
     * @var WishlistHelper
     */
    private $wishlistHelper;

    /**
     * @var LabelRendererFactory
     */
    private $labelRendererFactory;

    /**
     * @param LabelRendererFactory $labelRendererFactory
     * @param WishlistHelper $wishlistHelper
     */
    public function __construct(
        LabelRendererFactory $labelRendererFactory,
        WishlistHelper $wishlistHelper
    ) {
        $this->labelRendererFactory = $labelRendererFactory;
        $this->wishlistHelper = $wishlistHelper;
    }

    /**
     * Prepare wishlist item
     *
     * @param array $item
     * @return array
     */
    public function prepareWishlistItem($item)
    {
        $product = $this->findProduct($item);
        if ($product) {
            $item['image']['aw_onsale_label'] = $this->prepareLabelHtml($product);
            $item['image']['template'] = self::KO_TEMPLATE_FOR_WISHLIST_IMAGE;
        }

        return $item;
    }

    /**
     * Find and retrieve product from wishlist item
     *
     * @param array $item
     * @return Product|bool
     */
    private function findProduct($item)
    {
        $collection = $this->wishlistHelper->getWishlistItemCollection();
        foreach ($collection as $wishlistItem) {
            if ($wishlistItem->getProductId() == $item['product_id']) {
                return $wishlistItem->getProduct();
            }
        }

        return false;
    }

    /**
     * Prepare label html code
     *
     * @param Product $product
     * @return string
     */
    private function prepareLabelHtml($product)
    {
        /** @var LabelRenderer $labelRenderer */
        $labelRenderer = $this->labelRendererFactory->create(
            [
                'data' => [
                    'placement' => Placement::WISHLIST_SIDEBAR,
                    'product' => $product
                ]
            ]
        );

        return $labelRenderer->toHtml();
    }
}
