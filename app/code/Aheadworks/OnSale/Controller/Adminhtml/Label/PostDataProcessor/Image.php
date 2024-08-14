<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Controller\Adminhtml\Label\PostDataProcessor;

use Aheadworks\OnSale\Api\Data\LabelInterface;
use Aheadworks\OnSale\Model\Source\Label\Type;

/**
 * Class Image
 *
 * @package Aheadworks\OnSale\Controller\Adminhtml\Label\PostDataProcessor
 */
class Image implements ProcessorInterface
{
    /**
     * {@inheritdoc}
     */
    public function process($data)
    {
        if ($data[LabelInterface::TYPE] == Type::PICTURE) {
            $data[LabelInterface::IMG_FILE] = $data[LabelInterface::IMG_FILE][0]['file'];
        } else {
            $data[LabelInterface::IMG_FILE] = null;
        }
        return $data;
    }
}
