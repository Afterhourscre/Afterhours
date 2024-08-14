<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */


namespace Aheadworks\Faq\Model\Category\Source;

use Magento\Framework\Data\OptionSourceInterface;

class IsEnableFilter implements OptionSourceInterface
{
    /**
     * @var IsEnable
     */
    private $isEnable;

    /**
     * @param IsEnable $isEnable
     */
    public function __construct(IsEnable $isEnable)
    {
        $this->isEnable = $isEnable;
    }

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return $this->isEnable->toOptionArray();
    }
}
