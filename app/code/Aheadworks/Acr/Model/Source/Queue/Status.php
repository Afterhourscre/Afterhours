<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Acr\Model\Source\Queue;

use Aheadworks\Acr\Api\Data\QueueInterface;
use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Status
 * @package Aheadworks\Acr\Model\Source\Queue
 */
class Status implements OptionSourceInterface
{
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => QueueInterface::STATUS_PENDING,
                'label' => __('Pending')
            ],
            [
                'value' => QueueInterface::STATUS_SENT,
                'label' => __('Sent')
            ],
            [
                'value' => QueueInterface::STATUS_FAILED,
                'label' => __('Failed')
            ],
            [
                'value' => QueueInterface::STATUS_CANCELLED,
                'label' => __('Cancelled')
            ],
        ];
    }
}
