<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */


namespace Aheadworks\Faq\Model\Article\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * FAQ Article IsActiveFilter
 */
class IsActiveFilter implements OptionSourceInterface
{
    /**
     * @var IsActive $isActive
     */
    private $isActive;

    /**
     * @param IsActive $isActive
     */
    public function __construct(
        IsActive $isActive
    ) {
        $this->isActive = $isActive;
    }

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return $this->isActive->toOptionArray();
    }
}
