<?php
/**
 * Copyright © Mageside. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */
namespace Mageside\MultipleCustomForms\Model\Source;

use \Mageside\MultipleCustomForms\Block\Widget\CustomForm\Fields\Type\DefaultType;

class Validation extends DefaultType implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        $options = [];
        $options[] = ['value' => '', 'label' => ' '];
        foreach ($this->_validators as $key => $val) {
            $options[] = ['value' => $key, 'label' => $val['title']];
        }

        return $options;
    }
}
