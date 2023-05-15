<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Model\Label\Renderer\ConfigMetadata;

use Aheadworks\OnSale\Model\Config;
use Aheadworks\OnSale\Model\Label\Renderer\ConfigMetadata;
use Aheadworks\OnSale\Model\Source\Label\LineUpType;
use Magento\Framework\ObjectManagerInterface;
use Aheadworks\OnSale\Model\Label\Renderer\Placement\ConfigPool as PlacementConfigPool;
use Aheadworks\OnSale\Model\Label\Renderer\Placement\ConfigInterface;

/**
 * Class Resolver
 *
 * @package Aheadworks\OnSale\Model\Label\Renderer\ConfigMetadata
 */
class Resolver
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var PlacementConfigPool
     */
    private $placementConfigPool;

    /**
     * @var LineUpType
     */
    private $lineUpType;

    /**
     * @var Config
     */
    private $config;

    /**
     * @param ObjectManagerInterface $objectManager
     * @param PlacementConfigPool $placementConfigPool
     * @param LineUpType $lineUpType
     * @param Config $config
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        PlacementConfigPool $placementConfigPool,
        LineUpType $lineUpType,
        Config $config
    ) {
        $this->objectManager = $objectManager;
        $this->placementConfigPool = $placementConfigPool;
        $this->lineUpType = $lineUpType;
        $this->config = $config;
    }

    /**
     * Resolve renderer config by placement
     *
     * @param string $placement
     * @param string $area
     * @return ConfigMetadata
     * @throws \Exception
     */
    public function resolveByPlacement($placement, $area)
    {
        $placementConfig = $this->placementConfigPool->getConfigByPlacement($placement);
        return $this->createConfigMetadata($placementConfig, $area);
    }

    /**
     * Resolve renderer config by image
     *
     * @param string $image
     * @param string $area
     * @return ConfigMetadata
     * @throws \Exception
     */
    public function resolveByImage($image, $area)
    {
        $placementConfig = $this->placementConfigPool->getConfigByImage($image);
        return $this->createConfigMetadata($placementConfig, $area);
    }

    /**
     * Create config meta data
     *
     * @param ConfigInterface $placementConfig
     * @param string $area
     * @return ConfigMetadata
     * @throws \Exception
     */
    private function createConfigMetadata($placementConfig, $area)
    {
        $moveToSelectorByArea = $placementConfig->getMoveToSelectorByArea()[$area];
        $lineUpType = $this->config->getLabelsLineUpType();

        $labelMetadata = [
            ConfigMetadata::IN_PARENT_SELECTOR => $placementConfig->getInParentSelector(),
            ConfigMetadata::MOVE_TO_SELECTOR => $moveToSelectorByArea['move_to_selector'],
            ConfigMetadata::ACTION => $moveToSelectorByArea['action'],
            ConfigMetadata::AREA_ADDITIONAL_CLASSES => $moveToSelectorByArea['additional_classes'],
            ConfigMetadata::LABEL_ADDITIONAL_CLASSES => $this->lineUpType->getClassByLineUpType($lineUpType),
            ConfigMetadata::AREA_STYLESHEET => $this->getAreaStylesheet(),
            ConfigMetadata::LABEL_SIZE => $placementConfig->getSize()
        ];

        return $this->objectManager->create(ConfigMetadata::class, ['data' => $labelMetadata]);
    }

    /**
     * Retrieve area stylesheet
     *
     * @return string
     */
    private function getAreaStylesheet()
    {
        $lineUpType = $this->config->getLabelsLineUpType();
        $marginBetweenLabels = $this->config->getMarginBetweenLabels() ? : 0;
        $marginType = $lineUpType == LineUpType::HORIZONTAL ? 'left' : 'top';

        $stylesheet = '.aw-onsale__label-wrap > div:first-child { margin-' . $marginType . ': 0; }'
            . ' .aw-onsale__label-wrap > div { margin-' . $marginType . ': ' . $marginBetweenLabels . 'px; }';

        return $stylesheet;
    }
}
