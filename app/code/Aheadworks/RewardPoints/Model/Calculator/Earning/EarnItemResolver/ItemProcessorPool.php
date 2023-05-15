<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\RewardPoints\Model\Calculator\Earning\EarnItemResolver;

/**
 * Class ItemProcessorPool
 * @package Aheadworks\RewardPoints\Model\Calculator\Earning\EarnItemResolver
 */
class ItemProcessorPool
{
    /**
     * Default processor code
     */
    const DEFAULT_PROCESSOR_CODE = 'default';

    /**
     * @var ItemProcessorInterface[]
     */
    private $processors;

    /**
     * @param ItemProcessorInterface[] $processors
     */
    public function __construct(
        $processors = []
    ) {
        $this->processors = $processors;
    }

    /**
     * Get processors
     *
     * @return ItemProcessorInterface[]
     */
    public function getProcessors()
    {
        return $this->processors;
    }

    /**
     * Get processor by code
     *
     * @param string $code
     * @return ItemProcessorInterface
     * @throws \Exception
     */
    public function getProcessorByCode($code)
    {
        if (!isset($this->processors[$code])) {
            $code = self::DEFAULT_PROCESSOR_CODE;
        }
        $processor = $this->processors[$code];
        if (!$processor instanceof ItemProcessorInterface) {
            throw new \Exception('Item processor must implements ItemProcessorInterface');
        }

        return $processor;
    }
}
