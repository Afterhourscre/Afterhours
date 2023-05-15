<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\RewardPoints\Model\Repository;

use Aheadworks\RewardPoints\Model\ResourceModel\AbstractCollection;
use Magento\Framework\Api\SearchCriteria;

/**
 * Class CollectionProcessor
 * @package Aheadworks\RewardPoints\Model\Repository
 */
class CollectionProcessor implements CollectionProcessorInterface
{
    /**
     * @var CollectionProcessorInterface[]
     */
    private $processors;

    /**
     * @param CollectionProcessorInterface[] $processors
     */
    public function __construct(
        $processors = []
    ) {
        $this->processors = $processors;
    }

    /**
     * Process collection
     *
     * @param SearchCriteria $searchCriteria
     * @param AbstractCollection $collection
     * @throws \Exception
     */
    public function process($searchCriteria, $collection)
    {
        foreach ($this->processors as $processor) {
            if (!$processor instanceof CollectionProcessorInterface) {
                throw new \Exception('Collection processor must implement CollectionProcessorInterface!');
            }
            $processor->process($searchCriteria, $collection);
        }
    }
}
