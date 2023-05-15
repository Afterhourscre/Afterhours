<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\ViewModel\Label;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Aheadworks\OnSale\Api\BlockRepositoryInterface;
use Aheadworks\OnSale\Api\Data\BlockInterface;
use Aheadworks\OnSale\Block\Label\Renderer\Label;
use Aheadworks\OnSale\Model\Label\Renderer\ConfigMetadata;
use Magento\Catalog\Model\Product;
use Magento\Framework\Exception\LocalizedException;
use Aheadworks\OnSale\Model\Source\Label\Position\Area as AreaSource;
use Aheadworks\OnSale\Model\Source\Label\Position as PositionSource;
use Magento\Framework\View\LayoutInterface;

/**
 * Class Renderer
 *
 * @package Aheadworks\OnSale\ViewModel\Label
 */
class Renderer implements ArgumentInterface
{
    /**
     * @var ConfigMetadata\Resolver
     */
    private $configMetadataResolver;

    /**
     * @var AreaSource
     */
    private $areaSource;

    /**
     * @var PositionSource
     */
    private $positionSource;

    /**
     * @var BlockRepositoryInterface
     */
    private $blockRepository;

    /**
     * @var array
     */
    private $labelBlocks = [];

    /**
     * @var LayoutInterface
     */
    private $layout;

    /**
     * @param ConfigMetadata\Resolver $configMetadataResolver
     * @param AreaSource $areaSource
     * @param PositionSource $positionSource
     * @param BlockRepositoryInterface $blockRepository
     * @param LayoutInterface $layout
     */
    public function __construct(
        ConfigMetadata\Resolver $configMetadataResolver,
        AreaSource $areaSource,
        PositionSource $positionSource,
        BlockRepositoryInterface $blockRepository,
        LayoutInterface $layout
    ) {
        $this->configMetadataResolver = $configMetadataResolver;
        $this->areaSource = $areaSource;
        $this->positionSource = $positionSource;
        $this->blockRepository = $blockRepository;
        $this->layout = $layout;
    }

    /**
     * Retrieve label areas
     *
     * @return array
     */
    public function getLabelAreas()
    {
        return $this->areaSource->getAreaValues();
    }

    /**
     * Retrieve label config
     *
     * @param string $area
     * @param string|null $placement
     * @param string|null $image
     * @return ConfigMetadata
     * @throws \Exception
     * @throws LocalizedException
     */
    public function getLabelConfig($area, $placement, $image)
    {
        if ($placement) {
            return $this->configMetadataResolver->resolveByPlacement($placement, $area);
        }
        if ($image) {
            return $this->configMetadataResolver->resolveByImage($image, $area);
        }

        throw new LocalizedException(__('Label placement or image ID is required for rendering'));
    }

    /**
     * Retrieve css class by position
     *
     * @param string $position
     * @return string
     */
    public function getCssByPosition($position)
    {
        return $this->positionSource->getClassByPosition($position);
    }

    /**
     * Prepare label blocks by position
     *
     * @param BlockInterface[] $labelBlocks
     * @param string $position
     * @return BlockInterface[]
     */
    public function prepareLabelBlocksByPosition($labelBlocks, $position)
    {
        $preparedLabelBlocks = $labelBlocks;
        if ($this->positionSource->isInvertLabelByPosition($position)) {
            $preparedLabelBlocks = array_reverse($labelBlocks);
        }

        return $preparedLabelBlocks;
    }

    /**
     * Retrieve label blocks
     *
     * @param string $area
     * @param string $placement
     * @param Product $product
     * @param int $customerGroupId
     * @return BlockInterface[]
     */
    public function getLabelBlocksForArea($area, $placement, $product, $customerGroupId)
    {
        $labelBlocksForArea = [];
        $positionByAreaMap = $this->areaSource->getPositionByArea($area);
        foreach ($this->getLabelBlocks($placement, $product, $customerGroupId) as $labelBlock) {
            $labelPosition = $labelBlock->getLabel()->getPosition();
            if (in_array($labelPosition, $positionByAreaMap)) {
                $labelBlocksForArea[$labelPosition][] = $labelBlock;
            }
        }

        return $labelBlocksForArea;
    }

    /**
     * Retrieve label blocks
     *
     * @param string $placement
     * @param Product $product
     * @param int $customerGroupId
     * @return BlockInterface[]
     */
    public function getLabelBlocks($placement, $product, $customerGroupId)
    {
        $cacheKey = implode('-', [$placement, $product->getId(), $customerGroupId]);
        if (!isset($this->labelBlocks[$cacheKey])) {
            $this->labelBlocks[$cacheKey] = $this->blockRepository->getList(
                $product,
                $customerGroupId
            );
        }
        return $this->labelBlocks[$cacheKey];
    }

    /**
     * Create label block
     *
     * @param BlockInterface $labelBlock
     * @param ConfigMetadata $labelConfig
     * @return string
     */
    public function createLabelBlock($labelBlock, $labelConfig)
    {
        /** @var Label $block */
        $block = $this->layout->createBlock(Label::class);
        $labelBlock->setLabelSize($labelConfig->getLabelSize());
        $block
            ->setLabelBlock($labelBlock)
            ->setLabelConfig($labelConfig);

        return $block->toHtml();
    }
}
