<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Model\Label\Block\Rule;

use Aheadworks\OnSale\Model\Config;
use Aheadworks\OnSale\Model\Source\Label\Position\Area as PositionArea;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class LabelCounter
 *
 * @package Aheadworks\OnSale\Model\Label\Block
 */
class Counter
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var PositionArea
     */
    private $positionArea;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var array
     */
    private $areaCounter = [];

    /**
     * @param Config $config
     * @param PositionArea $positionArea
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Config $config,
        PositionArea $positionArea,
        StoreManagerInterface $storeManager
    ) {
        $this->config = $config;
        $this->positionArea = $positionArea;
        $this->storeManager = $storeManager;
    }

    /**
     * Reset counters
     *
     * @return $this
     */
    public function reset()
    {
        $this->areaCounter = [];
        foreach ($this->positionArea->getAreaValues() as $area) {
            $this->areaCounter[$area] = 0;
        }
        return $this;
    }

    /**
     * Check if the limit is reached
     *
     * @param string $position
     * @param int $storeId
     * @return bool
     */
    public function isLimitReached($position, $storeId)
    {
        $area = $this->positionArea->getAreaByPosition($position);
        $this->updateCounterByArea($area);
        $maxNumberByArea = $this->config->getMaxNumberOfLabelsByArea($area, $this->getWebsiteByStoreId($storeId));

        return $this->areaCounter[$area] > $maxNumberByArea;
    }

    /**
     * Update counter by area
     *
     * @param string $area
     * @return void
     */
    private function updateCounterByArea($area)
    {
        $this->areaCounter[$area]++;
    }

    /**
     * Retrieve website by store id
     *
     * @param $storeId
     * @return int|null
     */
    private function getWebsiteByStoreId($storeId)
    {
        try {
            $websiteId = $this->storeManager->getStore($storeId)->getWebsiteId();
        } catch (\Exception $exception) {
            $websiteId = null;
        }

        return $websiteId;
    }
}
