<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Class Config
 *
 * @package Aheadworks\OnSale\Model
 */
class Config
{
    /**#@+
     * Constants for config path
     */
    const XML_PATH_GENERAL_MARGIN_BETWEEN_LABELS = 'aw_onsale/general/margin_between_labels';
    const XML_PATH_GENERAL_LABELS_LINEUP_TYPE = 'aw_onsale/general/labels_lineup_type';
    const XML_PATH_GENERAL_MAX_NUMBER_OF_LABELS_PRODUCT_IMAGE = 'aw_onsale/general/max_number_of_labels_over_product';
    const XML_PATH_GENERAL_MAX_NUMBER_OF_LABELS_NEXT_TO_PRICE = 'aw_onsale/general/max_number_of_labels_next_to_price';
    /**#@-*/

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Retrieve margin (in pixels) between two labels
     *
     * @param int $websiteId|null
     * @return string
     */
    public function getMarginBetweenLabels($websiteId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_GENERAL_MARGIN_BETWEEN_LABELS,
            ScopeInterface::SCOPE_WEBSITE,
            $websiteId
        );
    }

    /**
     * Retrieve labels line-up type
     *
     * @param int $websiteId|null
     * @return string
     */
    public function getLabelsLineUpType($websiteId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_GENERAL_LABELS_LINEUP_TYPE,
            ScopeInterface::SCOPE_WEBSITE,
            $websiteId
        );
    }

    /**
     * Retrieve max number of labels by position area
     *
     * @param string $area
     * @param int $websiteId|null
     * @return int
     */
    public function getMaxNumberOfLabelsByArea($area, $websiteId = null)
    {
        return (int)$this->scopeConfig->getValue(
            constant('self::XML_PATH_GENERAL_MAX_NUMBER_OF_LABELS_' . strtoupper($area)),
            ScopeInterface::SCOPE_WEBSITE,
            $websiteId
        );
    }
}
