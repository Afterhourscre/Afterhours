<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Acr\Model\Rule\Condition\Product;

/**
 * Class Combine
 *
 * @package Aheadworks\Acr\Model\Rule\Condition\Product
 */
class Combine extends \Magento\CatalogRule\Model\Rule\Condition\Combine
{
    /**
     * {@inheritdoc}
     */
    public function getRemoveLinkHtml()
    {
        return '';
    }
}
