<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Model\Rule\LabelText\StoreResolver;

use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Reflection\DataObjectProcessor;
use Aheadworks\OnSale\Api\Data\LabelTextInterface;
use Aheadworks\OnSale\Api\Data\LabelTextInterfaceFactory;
use Aheadworks\OnSale\Api\Data\LabelTextStoreValueInterface;

/**
 * Class Converter
 *
 * @package Aheadworks\OnSale\Model\Rule\LabelText\StoreResolver
 */
class Converter
{
    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var DataObjectProcessor
     */
    private $dataObjectProcessor;

    /**
     * @var LabelTextInterfaceFactory
     */
    protected $labelTextFactory;

    /**
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param LabelTextInterfaceFactory $labelTextFactory
     */
    public function __construct(
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        LabelTextInterfaceFactory $labelTextFactory
    ) {
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->labelTextFactory = $labelTextFactory;
    }

    /**
     * Convert store value data to label text object
     *
     * @param LabelTextStoreValueInterface $storeValueObject
     * @return LabelTextInterface
     */
    public function convertToLabelTextObject($storeValueObject)
    {
        /** @var LabelTextInterface $labelTextFactory */
        $labelText = $this->labelTextFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $labelText,
            $this->dataObjectProcessor->buildOutputDataArray($storeValueObject, LabelTextStoreValueInterface::class),
            LabelTextInterface::class
        );

        return $labelText;
    }

    /**
     * Convert store value data to label text data array
     *
     * @param LabelTextStoreValueInterface $storeValueData
     * @return array
     */
    public function convertToLabelTextArray($storeValueData)
    {
        $labelTextObject = $this->convertToLabelTextObject($storeValueData);
        $labelTextDataArray = $this->dataObjectProcessor->buildOutputDataArray(
            $labelTextObject,
            LabelTextInterface::class
        );

        return $labelTextDataArray;
    }
}
