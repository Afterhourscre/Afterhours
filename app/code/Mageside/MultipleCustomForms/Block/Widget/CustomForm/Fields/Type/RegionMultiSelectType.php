<?php
/**
 * Copyright © Mageside. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */
namespace Mageside\MultipleCustomForms\Block\Widget\CustomForm\Fields\Type;

class RegionMultiSelectType extends \Mageside\MultipleCustomForms\Block\Widget\CustomForm\Fields\Type\RegionType
{
    public function getExtraParams()
    {
        return 'multiple="multiple" ' . $this->getValidation();
    }

    /**
     * @return array|mixed
     */
    public function getOptions()
    {
        return [];
    }
}
