<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Plugin\CustomerData;

use Magento\Wishlist\CustomerData\Wishlist;
use Magento\Wishlist\Helper\Data as WishlistHelper;
use Aheadworks\OnSale\Model\Label\Renderer\Placement\Block\WishlistSidebar;

/**
 * Class WishlistPlugin
 *
 * @package Aheadworks\OnSale\Plugin\CustomerData
 */
class WishlistPlugin
{
    /**
     * @var WishlistSidebar
     */
    private $wishlistSidebar;

    /**
     * @param WishlistSidebar $wishlistSidebar
     */
    public function __construct(
        WishlistSidebar $wishlistSidebar
    ) {
        $this->wishlistSidebar = $wishlistSidebar;
    }

    /**
     * Add label html data for rendering to result
     *
     * @param Wishlist $subject
     * @param array $result
     * @return array
     */
    public function afterGetSectionData($subject, $result)
    {
        $result = $this->prepareItemLabels($result);
        return $result;
    }

    /**
     * Prepare labels for wishlist items
     *
     * @param array $sectionData
     * @return array
     */
    private function prepareItemLabels($sectionData)
    {
        if (isset($sectionData['items']) && is_array($sectionData['items'])) {
            foreach ($sectionData['items'] as &$item) {
                $item = $this->wishlistSidebar->prepareWishlistItem($item);
            }
        }

        return $sectionData;
    }
}
