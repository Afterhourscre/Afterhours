<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Model\Label\Renderer\Placement;

use Magento\Framework\DataObject;

/**
 * Class Config
 *
 * @package Aheadworks\OnSale\Model\Label\Renderer\Placement
 */
class Config extends DataObject implements ConfigInterface
{
    /**
     * {@inheritdoc}
     */
    public function getMoveToSelectorByArea()
    {
        return $this->getData(self::MOVE_TO_SELECTOR_BY_AREA);
    }

    /**
     * {@inheritdoc}
     */
    public function getInParentSelector()
    {
        return $this->getData(self::IN_PARENT_SELECTOR);
    }

    /**
     * {@inheritdoc}
     */
    public function getSize()
    {
        return $this->getData(self::SIZE);
    }
}
