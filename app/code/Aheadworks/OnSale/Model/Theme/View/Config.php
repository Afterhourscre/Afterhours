<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Model\Theme\View;

use Magento\Framework\View\Config as ViewConfig;

/**
 * Class Config
 *
 * @package Aheadworks\OnSale\Model\Theme\View
 */
class Config
{
    /**
     * Module name used to retrieve data from view config
     */
    const MODULE_NAME = 'Aheadworks_OnSale';

    /**
     * Path in view config
     */
    const PLACEMENT_TYPE_CONFIG_PATH = 'config_data/placement';

    /**
     * @var ViewConfig
     */
    private $viewConfig;

    /**
     * @param ViewConfig $viewConfig
     */
    public function __construct(
        ViewConfig $viewConfig
    ) {
        $this->viewConfig = $viewConfig;
    }

    /**
     * Get var value
     *
     * @param string $var
     * @param array $params
     * @return array
     */
    public function getVarValue($var, $params = [])
    {
        $viewConfig = $this->viewConfig->getViewConfig($params);
        return $viewConfig->getVarValue(self::MODULE_NAME, $var);
    }

    /**
     * Get placement config
     *
     * @return array
     */
    public function getPlacementConfig()
    {
        return $this->getVarValue(self::PLACEMENT_TYPE_CONFIG_PATH);
    }
}
