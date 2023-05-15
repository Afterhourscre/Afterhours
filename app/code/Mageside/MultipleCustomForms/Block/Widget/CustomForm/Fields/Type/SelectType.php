<?php
/**
 * Copyright © Mageside. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */
namespace Mageside\MultipleCustomForms\Block\Widget\CustomForm\Fields\Type;

class SelectType extends \Mageside\MultipleCustomForms\Block\Widget\CustomForm\Fields\Type\DefaultType
{
    /**
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getSelectHtml()
    {
        $html = $this->getLayout()->createBlock(
            'Magento\Framework\View\Element\Html\Select'
        )->setName(
            $this->escapeHtmlAttr($this->getFieldHtmlName())
        )->setId(
            $this->escapeHtmlAttr($this->getFieldHtmlId())
        )->setTitle(
            $this->escapeHtmlAttr(__($this->getField()->getTitle()))
        )->setValue(
            $this->getDefaultValue()
        )->setOptions(
            $this->getOptions()
        )->setExtraParams(
            $this->getExtraParams()
        )->getHtml();

        return $html;
    }

    /**
     * @return string
     */
    public function getExtraParams()
    {
        return $this->getValidation();
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->_field->getOptions();
    }
}
