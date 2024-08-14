<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Model;

use Aheadworks\OnSale\Api\Data\LabelInterface;
use Magento\Framework\Model\AbstractModel;
use Aheadworks\OnSale\Model\ResourceModel\Label as ResourceLabel;

/**
 * Class Label
 *
 * @package Aheadworks\OnSale\Model
 */
class Label extends AbstractModel implements LabelInterface
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(ResourceLabel::class);
    }

    /**
     * {@inheritdoc}
     */
    public function getLabelId()
    {
        return $this->getData(self::LABEL_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setLabelId($labelId)
    {
        return $this->setData(self::LABEL_ID, $labelId);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->getData(self::NAME);
    }

    /**
     * {@inheritdoc}
     */
    public function setName($name)
    {
        return $this->setData(self::NAME, $name);
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return $this->getData(self::TYPE);
    }

    /**
     * {@inheritdoc}
     */
    public function setType($type)
    {
        return $this->setData(self::TYPE, $type);
    }

    /**
     * {@inheritdoc}
     */
    public function getPosition()
    {
        return $this->getData(self::POSITION);
    }

    /**
     * {@inheritdoc}
     */
    public function setPosition($position)
    {
        return $this->setData(self::POSITION, $position);
    }

    /**
     * {@inheritdoc}
     */
    public function getShapeType()
    {
        return $this->getData(self::SHAPE_TYPE);
    }

    /**
     * {@inheritdoc}
     */
    public function setShapeType($shapeType)
    {
        return $this->setData(self::SHAPE_TYPE, $shapeType);
    }

    /**
     * {@inheritdoc}
     */
    public function getImgFile()
    {
        return $this->getData(self::IMG_FILE);
    }

    /**
     * {@inheritdoc}
     */
    public function setImgFile($imgFile)
    {
        return $this->setData(self::IMG_FILE, $imgFile);
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomizeCssContainerLarge()
    {
        return $this->getData(self::CUSTOMIZE_CSS_CONTAINER_LARGE);
    }

    /**
     * {@inheritdoc}
     */
    public function setCustomizeCssContainerLarge($customizeCssContainerLarge)
    {
        return $this->setData(self::CUSTOMIZE_CSS_CONTAINER_LARGE, $customizeCssContainerLarge);
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomizeCssLabelLarge()
    {
        return $this->getData(self::CUSTOMIZE_CSS_LABEL_LARGE);
    }

    /**
     * {@inheritdoc}
     */
    public function setCustomizeCssLabelLarge($customizeCssLabelLarge)
    {
        return $this->setData(self::CUSTOMIZE_CSS_LABEL_LARGE, $customizeCssLabelLarge);
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomizeCssContainerMedium()
    {
        return $this->getData(self::CUSTOMIZE_CSS_CONTAINER_MEDIUM);
    }

    /**
     * {@inheritdoc}
     */
    public function setCustomizeCssContainerMedium($customizeCssContainerMedium)
    {
        return $this->setData(self::CUSTOMIZE_CSS_CONTAINER_MEDIUM, $customizeCssContainerMedium);
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomizeCssLabelMedium()
    {
        return $this->getData(self::CUSTOMIZE_CSS_LABEL_MEDIUM);
    }

    /**
     * {@inheritdoc}
     */
    public function setCustomizeCssLabelMedium($customizeCssLabelMedium)
    {
        return $this->setData(self::CUSTOMIZE_CSS_LABEL_MEDIUM, $customizeCssLabelMedium);
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomizeCssContainerSmall()
    {
        return $this->getData(self::CUSTOMIZE_CSS_CONTAINER_SMALL);
    }

    /**
     * {@inheritdoc}
     */
    public function setCustomizeCssContainerSmall($customizeCssContainerSmall)
    {
        return $this->setData(self::CUSTOMIZE_CSS_CONTAINER_SMALL, $customizeCssContainerSmall);
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomizeCssLabelSmall()
    {
        return $this->getData(self::CUSTOMIZE_CSS_LABEL_SMALL);
    }

    /**
     * {@inheritdoc}
     */
    public function setCustomizeCssLabelSmall($customizeCssLabelSmall)
    {
        return $this->setData(self::CUSTOMIZE_CSS_LABEL_SMALL, $customizeCssLabelSmall);
    }

    /**
     * Get customize css container for label size
     *
     * @param string|null $size
     * @return string
     */
    public function getCustomizeCssContainer($size = null)
    {
        return $this->getData(self::CUSTOMIZE_CSS_CONTAINER . '_' . $size);
    }

    /**
     * Get customize css label for label size
     *
     * @param string|null $size
     * @return string
     */
    public function getCustomizeCssLabel($size = null)
    {
        return $this->getData(self::CUSTOMIZE_CSS_LABEL . '_' . $size);
    }

    /**
     * {@inheritdoc}
     */
    public function getExtensionAttributes()
    {
        return $this->getData(self::EXTENSION_ATTRIBUTES_KEY);
    }

    /**
     * {@inheritdoc}
     */
    public function setExtensionAttributes(
        \Aheadworks\OnSale\Api\Data\LabelExtensionInterface $extensionAttributes
    ) {
        return $this->setData(self::EXTENSION_ATTRIBUTES_KEY, $extensionAttributes);
    }
}
