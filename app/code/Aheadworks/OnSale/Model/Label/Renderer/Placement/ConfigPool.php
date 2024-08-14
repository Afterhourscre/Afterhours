<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Model\Label\Renderer\Placement;

use Aheadworks\OnSale\Model\Theme\View\Config as ViewConfig;
use Aheadworks\OnSale\Model\Source\Label\Renderer\Placement;

/**
 * Class ConfigPool
 *
 * @package Aheadworks\OnSale\Model\Label\Renderer\Placement
 */
class ConfigPool
{
    /**
     * @var ConfigInterfaceFactory
     */
    private $configFactory;

    /**
     * @var ViewConfig
     */
    private $viewConfig;

    /**
     * @var array
     */
    private $configData = [];

    /**
     * @var ConfigInterface[]
     */
    private $configInstances = [];

    /**
     * @var array
     */
    private $mergedConfig = [];

    /**
     * @param ConfigInterfaceFactory $configFactory
     * @param ViewConfig $viewConfig
     * @param array $configData
     */
    public function __construct(
        ConfigInterfaceFactory $configFactory,
        ViewConfig $viewConfig,
        $configData = []
    ) {
        $this->configFactory = $configFactory;
        $this->configData = $configData;
        $this->viewConfig = $viewConfig;
    }

    /**
     * Retrieves config instance by placement
     *
     * @param string $placement
     * @return ConfigInterface
     * @throws \Exception
     */
    public function getConfigByPlacement($placement)
    {
        if (empty($this->configInstances[$placement])) {
            $configDataForPlacement = $this->getConfigDataForPlacement($placement);
            $configInstance = $this->getConfigInstance($configDataForPlacement);
            $this->configInstances[$placement] = $configInstance;
        }
        return $this->configInstances[$placement];
    }

    /**
     * Retrieves config instance by image
     *
     * @param string $image
     * @return ConfigInterface
     * @throws \Exception
     */
    public function getConfigByImage($image)
    {
        $config = $this->prepareConfig();

        $placement = Placement::PRODUCT_LIST;
        foreach ($config as $placementKey => $placementData) {
            if (isset($placementData['applicable_image'][$image])) {
                $placement = $placementKey;
                break;
            }
        }

        return $this->getConfigByPlacement($placement);
    }

    /**
     * Retrieve config data for specified placement
     *
     * @param int $placement
     * @return array
     * @throws \Exception
     */
    private function getConfigDataForPlacement($placement)
    {
        $config = $this->prepareConfig();
        if (!isset($config[$placement])) {
            throw new \Exception(sprintf('Unknown placement: %s requested', $placement));
        }
        return $config[$placement];
    }

    /**
     * Retrieve config instance from config data
     *
     * @param array $configData
     * @return ConfigInterface
     * @throws \Exception
     */
    private function getConfigInstance($configData)
    {
        $configInstance = $this->configFactory->create(['data' => $configData]);
        if (!$configInstance instanceof ConfigInterface) {
            throw new \Exception(
                sprintf('Config instance does not implement required interface.')
            );
        }
        return $configInstance;
    }

    /**
     * Prepare config
     *
     * Config is created from view.xml and di.xml. DI is deprecated.
     *
     * @return array
     */
    private function prepareConfig()
    {
        if (empty($this->mergedConfig)) {
            $this->mergedConfig = $this->viewConfig->getPlacementConfig();
            $this->mergedConfig = array_replace_recursive($this->mergedConfig, $this->configData);
        }

        return $this->mergedConfig;
    }
}
