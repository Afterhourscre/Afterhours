<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Ui\Component\Listing\Label\Column\LabelView\Renderer;

use Aheadworks\OnSale\Api\Data\LabelInterface;
use Aheadworks\OnSale\Api\Data\LabelInterfaceFactory;
use Aheadworks\OnSale\Api\LabelRepositoryInterface;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class LabelResolver
 *
 * @package Aheadworks\OnSale\Ui\Component\Listing\Label\Column\LabelView\Renderer
 */
class LabelResolver
{
    /**
     * @var LabelRepositoryInterface
     */
    private $labelRepository;

    /**
     * @var LabelInterfaceFactory
     */
    private $labelFactory;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @param LabelRepositoryInterface $labelRepository
     * @param LabelInterfaceFactory $labelFactory
     * @param DataObjectHelper $dataObjectHelper
     */
    public function __construct(
        LabelRepositoryInterface $labelRepository,
        LabelInterfaceFactory $labelFactory,
        DataObjectHelper $dataObjectHelper
    ) {
        $this->labelRepository = $labelRepository;
        $this->labelFactory = $labelFactory;
        $this->dataObjectHelper = $dataObjectHelper;
    }

    /**
     * Resolve label object by label data
     *
     * @param array|int $label
     * @return LabelInterface
     */
    public function resolve($label)
    {
        if (is_array($label)) {
            /** @var LabelInterface $labelObject */
            $labelObject = $this->labelFactory->create();
            $this->dataObjectHelper->populateWithArray(
                $labelObject,
                $label,
                LabelInterface::class
            );
        } elseif (is_numeric($label)) {
            try {
                $labelObject = $this->labelRepository->get($label);
            } catch (NoSuchEntityException $e) {
                $labelObject = $this->labelFactory->create();
            }
        } else {
            $labelObject = $this->labelFactory->create();
        }

        return $labelObject;
    }
}
