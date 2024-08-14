<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Model\Label\Renderer;

use Magento\Framework\DataObject;

/**
 * Class ConfigMetadata
 *
 * @package Aheadworks\OnSale\Model\Label\Renderer
 */
class ConfigMetadata extends DataObject
{
    /**#@+
     * Constants defined for keys of the data array.
     * Identical to the name of the getter in snake case
     */
    const MOVE_TO_SELECTOR = 'move_to_selector';
    const ACTION = 'action';
    const IN_PARENT_SELECTOR = 'in_parent_selector';
    const AREA_ADDITIONAL_CLASSES = 'area_additional_classes';
    const AREA_STYLESHEET = 'area_stylesheet';
    const LABEL_ADDITIONAL_CLASSES = 'label_additional_classes';
    const LABEL_SIZE = 'label_size';
    /**#@-*/

    /**
     * Get move to selector
     *
     * @return string
     */
    public function getMoveToSelector()
    {
        return $this->getData(self::MOVE_TO_SELECTOR);
    }

    /**
     * Get action
     *
     * @return string
     */
    public function getAction()
    {
        return $this->getData(self::ACTION);
    }

    /**
     * Get in parent selector
     *
     * @return string
     */
    public function getInParentSelector()
    {
        return $this->getData(self::IN_PARENT_SELECTOR);
    }

    /**
     * Get area additional classes
     *
     * @return string
     */
    public function getAreaAdditionalClasses()
    {
        return $this->getData(self::AREA_ADDITIONAL_CLASSES);
    }

    /**
     * Get area stylesheet
     *
     * @return string
     */
    public function getAreaStylesheet()
    {
        return $this->getData(self::AREA_STYLESHEET);
    }

    /**
     * Get label additional classes
     *
     * @return string
     */
    public function getLabelAdditionalClasses()
    {
        return $this->getData(self::LABEL_ADDITIONAL_CLASSES);
    }

    /**
     * Get label size
     *
     * @return string
     */
    public function getLabelSize()
    {
        return $this->getData(self::LABEL_SIZE);
    }
}
