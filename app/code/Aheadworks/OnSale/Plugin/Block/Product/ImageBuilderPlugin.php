<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Plugin\Block\Product;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Block\Product\ImageBuilder;
use Magento\Catalog\Block\Product\Image;

/**
 * Class ImageBuilderPlugin
 *
 * @package Aheadworks\OnSale\Plugin\Block\Product
 */
class ImageBuilderPlugin
{
    /**
     * @var Product
     *
     * State variable is used for compatibility with 2.2.X
     */
    private $product;

    /**
     * @var string
     *
     * State variable is used for compatibility with 2.2.X
     */
    private $imageId;

    /**
     * Store product object
     *
     * The way value is set to builder is used in 2.2.X
     *
     * @param ImageBuilder $subject
     * @param Product $product
     * @return array
     */
    public function beforeSetProduct($subject, $product)
    {
        $this->product = $product;
        return [$product];
    }

    /**
     * Store image identifier
     *
     * The way value is set to builder is used in 2.2.X
     *
     * @param ImageBuilder $subject
     * @param string $imageId
     * @return array
     */
    public function beforeSetImageId($subject, $imageId)
    {
        $this->imageId = $imageId;
        return [$imageId];
    }

    /**
     * Add product object to data of image block
     *
     * @param ImageBuilder $subject
     * @param Image $imageBlock
     * @param Product|null $product
     * @param string|null $imageId
     * @return Image
     */
    public function afterCreate($subject, $imageBlock, $product = null, $imageId = null)
    {
        $product = $product ?: $this->product;
        $imageId = $imageId ?: $this->imageId;
        if ($product) {
            $imageBlock->setProduct($product);
            $imageBlock->setImageId($imageId);
            $this->product = null;
            $this->imageId = null;
        }

        return $imageBlock;
    }
}
