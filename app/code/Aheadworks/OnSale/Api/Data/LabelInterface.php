<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Label interface
 * @api
 */
interface LabelInterface extends ExtensibleDataInterface
{
    /**#@+
     * Constants defined for keys of the data array.
     * Identical to the name of the getter in snake case
     */
    const LABEL_ID = 'label_id';
    const NAME = 'name';
    const TYPE = 'type';
    const POSITION = 'position';
    const SHAPE_TYPE = 'shape_type';
    const IMG_FILE = 'img_file';
    const CUSTOMIZE_CSS_CONTAINER = 'customize_css_container';
    const CUSTOMIZE_CSS_LABEL = 'customize_css_label';
    const CUSTOMIZE_CSS_CONTAINER_LARGE = 'customize_css_container_large';
    const CUSTOMIZE_CSS_LABEL_LARGE = 'customize_css_label_large';
    const CUSTOMIZE_CSS_CONTAINER_MEDIUM = 'customize_css_container_medium';
    const CUSTOMIZE_CSS_LABEL_MEDIUM = 'customize_css_label_medium';
    const CUSTOMIZE_CSS_CONTAINER_SMALL = 'customize_css_container_small';
    const CUSTOMIZE_CSS_LABEL_SMALL = 'customize_css_label_small';
    /**#@-*/

    /**
     * Get Label ID
     *
     * @return int
     */
    public function getLabelId();

    /**
     * Set Label ID
     *
     * @param int $labelId
     * @return $this
     */
    public function setLabelId($labelId);

    /**
     * Get name
     *
     * @return string
     */
    public function getName();

    /**
     * Set name
     *
     * @param string $name
     * @return $this
     */
    public function setName($name);

    /**
     * Get type
     *
     * @return string
     */
    public function getType();

    /**
     * Set type
     *
     * @param string $type
     * @return $this
     */
    public function setType($type);

    /**
     * Get position
     *
     * @return string
     */
    public function getPosition();

    /**
     * Set position
     *
     * @param string $position
     * @return $this
     */
    public function setPosition($position);

    /**
     * Get shape type
     *
     * @return string
     */
    public function getShapeType();

    /**
     * Set shape type
     *
     * @param string $shapeType
     * @return $this
     */
    public function setShapeType($shapeType);

    /**
     * Get image file name
     *
     * @return string
     */
    public function getImgFile();

    /**
     * Set image file name
     *
     * @param string $imgFile
     * @return $this
     */
    public function setImgFile($imgFile);

    /**
     * Get customize css container large
     *
     * @return string
     */
    public function getCustomizeCssContainerLarge();

    /**
     * Set customize css container large
     *
     * @param string $customizeCssContainerLarge
     * @return $this
     */
    public function setCustomizeCssContainerLarge($customizeCssContainerLarge);

    /**
     * Get customize css label large
     *
     * @return string
     */
    public function getCustomizeCssLabelLarge();

    /**
     * Set customize css label large
     *
     * @param string $customizeCssLabelLarge
     * @return $this
     */
    public function setCustomizeCssLabelLarge($customizeCssLabelLarge);

    /**
     * Get customize css container medium
     *
     * @return string
     */
    public function getCustomizeCssContainerMedium();

    /**
     * Set customize css container medium
     *
     * @param string $customizeCssContainerMedium
     * @return $this
     */
    public function setCustomizeCssContainerMedium($customizeCssContainerMedium);

    /**
     * Get customize css label medium
     *
     * @return string
     */
    public function getCustomizeCssLabelMedium();

    /**
     * Set customize css label medium
     *
     * @param string $customizeCssLabelMedium
     * @return $this
     */
    public function setCustomizeCssLabelMedium($customizeCssLabelMedium);

    /**
     * Get customize css container small
     *
     * @return string
     */
    public function getCustomizeCssContainerSmall();

    /**
     * Set customize css container small
     *
     * @param string $customizeCssContainerSmall
     * @return $this
     */
    public function setCustomizeCssContainerSmall($customizeCssContainerSmall);

    /**
     * Get customize css label medium
     *
     * @return string
     */
    public function getCustomizeCssLabelSmall();

    /**
     * Set customize css label medium
     *
     * @param string $customizeCssLabelSmall
     * @return $this
     */
    public function setCustomizeCssLabelSmall($customizeCssLabelSmall);

    /**
     * Retrieve existing extension attributes object or create a new one
     *
     * @return \Aheadworks\OnSale\Api\Data\LabelExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object
     *
     * @param \Aheadworks\OnSale\Api\Data\LabelExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Aheadworks\OnSale\Api\Data\LabelExtensionInterface $extensionAttributes
    );
}
