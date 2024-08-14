<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Ui\Component\Listing\Label\Column;

use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Class RuleNames
 *
 * @package Aheadworks\OnSale\Ui\Component\Listing\Label
 */
class RuleNames extends Column
{
    /**
     * {@inheritdoc}
     */
    public function prepareDataSource(array $dataSource)
    {
        $fieldName = $this->getData('name');
        foreach ($dataSource['data']['items'] as & $item) {
            $item[$fieldName] = implode(',', $item[$fieldName]);
        }

        return $dataSource;
    }
}
