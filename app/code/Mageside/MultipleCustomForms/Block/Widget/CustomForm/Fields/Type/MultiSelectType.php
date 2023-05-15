<?php
/**
 * Copyright © Mageside. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */
namespace Mageside\MultipleCustomForms\Block\Widget\CustomForm\Fields\Type;

class MultiSelectType extends \Mageside\MultipleCustomForms\Block\Widget\CustomForm\Fields\Type\SelectType
{
    public function getExtraParams()
    {
        return 'multiple="multiple" ' . $this->getValidation();
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->_field->getOptions(false);
    }
}
