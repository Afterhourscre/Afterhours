<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Acr\Ui\Component\Listing\Columns\Rule;

use Magento\Store\Ui\Component\Listing\Column\Store as StoreColumn;

/**
 * Class Store
 * @package Aheadworks\Acr\Ui\Component\Listing\Columns\Rule
 * @codeCoverageIgnore
 */
class Store extends StoreColumn
{
    /**
     * Prepare data source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        $this->storeKey = 'store_ids';
        return parent::prepareDataSource($dataSource);
    }
}
