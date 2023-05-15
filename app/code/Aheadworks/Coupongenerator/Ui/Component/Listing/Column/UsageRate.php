<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Coupongenerator\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;

/**
 * Class UsageRate
 * @package Aheadworks\Coupongenerator\Ui\Component\Listing\Column
 * @codeCoverageIgnore
 */
class UsageRate extends \Magento\Ui\Component\Listing\Columns\Column
{
    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare data source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        foreach ($dataSource['data']['items'] as &$item) {
            $item['usage_rate'] = $this->getUsageRate($item['usage_rate']);
        }

        return $dataSource;
    }

    /**
     * Get usage rate value
     *
     * @param string $usageRate
     * @return string
     */
    private function getUsageRate($usageRate)
    {
        return $usageRate . '%';
    }
}
