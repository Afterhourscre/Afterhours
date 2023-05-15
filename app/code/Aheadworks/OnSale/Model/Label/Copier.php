<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Model\Label;

use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Aheadworks\OnSale\Api\Data\LabelInterface;
use Aheadworks\OnSale\Api\LabelRepositoryInterface;
use Aheadworks\OnSale\Api\Data\LabelInterfaceFactory;

/**
 * Class Copier
 *
 * @package Aheadworks\OnSale\Model\Label
 */
class Copier
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
     * @var LabelRepositoryInterface
     */
    private $labelRepository;

    /**
     * @var LabelInterfaceFactory
     */
    private $labelFactory;

    /**
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param LabelRepositoryInterface $labelRepository
     * @param LabelInterfaceFactory $labelFactory
     */
    public function __construct(
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        LabelRepositoryInterface $labelRepository,
        LabelInterfaceFactory $labelFactory
    ) {
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->labelRepository = $labelRepository;
        $this->labelFactory = $labelFactory;
    }

    /**
     * Create a label duplicate
     *
     * @param LabelInterface $label
     * @return LabelInterface
     * @throws LocalizedException
     */
    public function copy(LabelInterface $label)
    {
        $newLabelData = $this->prepareNewLabelData($label);
        $newLabel = $this->convertLabelDataToObject($newLabelData);

        $this->labelRepository->save($newLabel);
        return $newLabel;
    }

    /**
     * Prepare data for new label
     *
     * @param LabelInterface $label
     * @return array
     */
    private function prepareNewLabelData(LabelInterface $label)
    {
        $labelData = $this->dataObjectProcessor->buildOutputDataArray(
            $label,
            LabelInterface::class
        );

        unset($labelData[LabelInterface::LABEL_ID]);
        return $labelData;
    }

    /**
     * Convert label data to object
     *
     * @param array $data
     * @return LabelInterface
     */
    private function convertLabelDataToObject($data)
    {
        /** @var LabelInterface $object */
        $object = $this->labelFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $object,
            $data,
            LabelInterface::class
        );

        return $object;
    }
}
