<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Controller\Adminhtml\Label\PostDataProcessor;

use Aheadworks\OnSale\Api\Data\LabelInterface;
use Aheadworks\OnSale\Model\Source\Label\Type;
use Aheadworks\OnSale\Model\Source\Label\Shape\Type as ShapeType;

/**
 * Class Shape
 *
 * @package Aheadworks\OnSale\Controller\Adminhtml\Label\PostDataProcessor
 */
class Shape implements ProcessorInterface
{
    /**
     * {@inheritdoc}
     */
    public function process($data)
    {
        if ($data[LabelInterface::TYPE] != Type::SHAPE) {
            $data[LabelInterface::SHAPE_TYPE] = null;
        } else {
            $data[LabelInterface::SHAPE_TYPE] = $data[LabelInterface::SHAPE_TYPE] ? : ShapeType::RECTANGLE;
        }
        return $data;
    }
}
