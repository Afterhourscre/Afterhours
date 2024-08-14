<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Model\Label\Block\Layout\Processor;

use Aheadworks\OnSale\Api\Data\LabelInterface;
use Aheadworks\OnSale\Model\Label\Image\Info;
use Aheadworks\OnSale\Model\Source\Label\Type;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Stdlib\ArrayManager;

/**
 * Class PictureInfo
 *
 * @package Aheadworks\OnSale\Model\Label\Block\Layout\Processor
 */
class PictureInfo implements LayoutProcessorInterface
{
    /**
     * @var ArrayManager
     */
    private $arrayManager;

    /**
     * @var Info
     */
    private $imageInfo;

    /**
     * @param ArrayManager $arrayManager
     * @param Info $imageInfo
     */
    public function __construct(
        ArrayManager $arrayManager,
        Info $imageInfo
    ) {
        $this->arrayManager = $arrayManager;
        $this->imageInfo = $imageInfo;
    }

    /**
     * {@inheritdoc}
     */
    public function process($jsLayout, $labelBlock, $scope)
    {
        $label = $labelBlock->getLabel();
        $component = 'components/' . $scope;
        $jsLayout = $this->arrayManager->merge(
            $component,
            $jsLayout,
            [
                'pictureInfo' => $this->preparePictureInfo($label),
            ]
        );

        return $jsLayout;
    }

    /**
     * Prepare picture info
     *
     * @param LabelInterface $label
     * @return array
     */
    private function preparePictureInfo($label)
    {
        $pictureInfo = [];
        if ($label->getType() == Type::PICTURE) {
            try {
                $pictureInfo[0]['url'] = $this->imageInfo->getMediaUrl($label->getImgFile());
            } catch (NoSuchEntityException $e) {
            }
        }

        return $pictureInfo;
    }
}
