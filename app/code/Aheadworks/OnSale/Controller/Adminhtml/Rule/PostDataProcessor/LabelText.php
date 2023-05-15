<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Controller\Adminhtml\Rule\PostDataProcessor;

use Aheadworks\OnSale\Api\Data\RuleInterface;
use Aheadworks\OnSale\Api\Data\LabelTextStoreValueInterface;
use Aheadworks\OnSale\Controller\Adminhtml\Label\PostDataProcessor\ProcessorInterface;

/**
 * Class PostDataProcessor
 *
 * @package Aheadworks\OnSale\Controller\Adminhtml\Rule
 */
class LabelText implements ProcessorInterface
{
    /**
     *  Prepare label text data for save
     *
     * @param array $data
     * @return array
     */
    public function process($data)
    {
        $data[RuleInterface::FRONTEND_LABEL_TEXT_STORE_VALUES] = $this->prepareFrontendLabelTextData($data);
        return $data;
    }

    /**
     * Prepare frontend label text data
     *
     * @param array $data
     * @return array
     */
    private function prepareFrontendLabelTextData($data)
    {
        $frontendLabelTextData = [];
        if (isset($data[RuleInterface::FRONTEND_LABEL_TEXT_STORE_VALUES])) {
            $labelTextValues = $data[RuleInterface::FRONTEND_LABEL_TEXT_STORE_VALUES];
            foreach ($labelTextValues as $labelTextValue) {
                if ($labelTextValue[LabelTextStoreValueInterface::VALUE_LARGE]) {
                    $frontendLabelTextData[] = $labelTextValue;
                }
            }
        }

        return $frontendLabelTextData;
    }
}
