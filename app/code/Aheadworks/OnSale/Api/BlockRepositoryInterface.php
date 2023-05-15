<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Api;

/**
 * Interface BlockRepositoryInterface
 * @api
 */
interface BlockRepositoryInterface
{
    /**
     * Retrieve labels block matching the specified criteria
     *
     * @param \Magento\Catalog\Api\Data\ProductInterface $product
     * @param int $customerGroupId
     * @return \Aheadworks\OnSale\Api\Data\BlockInterface[]
     */
    public function getList($product, $customerGroupId);
}
