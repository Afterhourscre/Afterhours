<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Model\ResourceModel\Label;

use Aheadworks\OnSale\Api\Data\LabelInterface;
use Aheadworks\OnSale\Model\ResourceModel\AbstractCollection;
use Aheadworks\OnSale\Model\ResourceModel\Label as ResourceLabel;
use Aheadworks\OnSale\Model\Label;

/**
 * Class Collection
 *
 * @package Aheadworks\OnSale\Model\ResourceModel\Label
 */
class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = Label::LABEL_ID;

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(Label::class, ResourceLabel::class);
    }

    /**
     * {@inheritdoc}
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->addFilterToMap(LabelInterface::LABEL_ID, 'main_table.label_id');
        $this->addFilterToMap(LabelInterface::NAME, 'main_table.name');

        return $this;
    }
}
