<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\RewardPoints\Ui\Component\Listing\Columns\EarnRule;

use Aheadworks\RewardPoints\Api\Data\EarnRuleInterface;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Class Link
 * @package Aheadworks\RewardPoints\Ui\Component\Listing\Columns\EarnRule
 */
class Link extends Column
{
    /**
     * Prepare data source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        foreach ($dataSource['data']['items'] as &$item) {
            if (!isset($item['link'])) {
                $item['link'] = $this->context->getUrl(
                    'aw_reward_points/earning_rules/edit',
                    ['id' => $item[EarnRuleInterface::ID]]
                );
            }
        }

        return $dataSource;
    }
}
