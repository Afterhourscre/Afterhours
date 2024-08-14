<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Controller\Adminhtml\Rule;

use Aheadworks\OnSale\Api\Data\RuleInterface;
use Aheadworks\OnSale\Api\Data\RuleInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Reflection\DataObjectProcessor;
use Aheadworks\OnSale\Model\Rule\ReindexNotice;

/**
 * Class PostDataUpdateChecker
 *
 * @package Aheadworks\OnSale\Controller\Adminhtml\Rule
 */
class PostDataUpdateChecker
{
    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var RuleInterfaceFactory
     */
    private $ruleDataFactory;

    /**
     * @var DataObjectProcessor
     */
    private $dataObjectProcessor;

    /**
     * @var ReindexNotice
     */
    private $reindexNotice;

    /**
     * @param DataObjectHelper $dataObjectHelper
     * @param RuleInterfaceFactory $ruleDataFactory
     * @param DataObjectProcessor $dataObjectProcessor
     * @param ReindexNotice $reindexNotice
     */
    public function __construct(
        DataObjectHelper $dataObjectHelper,
        RuleInterfaceFactory $ruleDataFactory,
        DataObjectProcessor $dataObjectProcessor,
        ReindexNotice $reindexNotice
    ) {
        $this->dataObjectHelper = $dataObjectHelper;
        $this->ruleDataFactory = $ruleDataFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->reindexNotice = $reindexNotice;
    }

    /**
     * Check if post parameters have been changed and enable reindex notice
     *    if changes found
     *
     * @param array|RuleInterface $newRuleData
     * @param RuleInterface $oldRuleDataObject
     * @throws \Exception
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function checkForReindexNotice($newRuleData, $oldRuleDataObject)
    {
        if (is_array($newRuleData)) {
            $newRuleDataArray = $this->getDataArrayFromPostData($newRuleData);
        } else {
            $newRuleDataArray = $this->getDataArrayFromDataObject($newRuleData);
        }
        $oldRuleDataArray = $this->getDataArrayFromDataObject($oldRuleDataObject);
        $arrayDiff = $this->dataDiff($newRuleDataArray, $oldRuleDataArray);
        unset($arrayDiff[RuleInterface::NAME]);
        if (!empty($arrayDiff)) {
            $this->reindexNotice->setEnabled();
        }
    }

    /**
     * Retrieves data object using model
     *
     * @param RuleInterface $model
     * @return array
     */
    private function getDataArrayFromDataObject($model)
    {
        return $this->dataObjectProcessor->buildOutputDataArray($model, RuleInterface::class);
    }

    /**
     * Retrieves data object using array
     *
     * @param array $postData
     * @return array
     */
    private function getDataArrayFromPostData($postData)
    {
        $ruleModel = $this->ruleDataFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $ruleModel,
            $postData,
            RuleInterface::class
        );
        return $this->getDataArrayFromDataObject($ruleModel);
    }

    /**
     * Get array with data differences
     *
     * @param array $array1
     * @param array $array2
     *
     * @return array
     */
    private function dataDiff($array1, $array2)
    {
        $result = [];
        foreach ($array1 as $key => $value) {
            if (array_key_exists($key, $array2)) {
                if ($value != $array2[$key]) {
                    $result[$key] = true;
                }
            } else {
                $result[$key] = true;
            }
        }
        return $result;
    }
}
