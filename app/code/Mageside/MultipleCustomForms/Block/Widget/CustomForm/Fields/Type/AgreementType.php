<?php
/**
 * Copyright © Mageside. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */
namespace Mageside\MultipleCustomForms\Block\Widget\CustomForm\Fields\Type;

class AgreementType extends \Mageside\MultipleCustomForms\Block\Widget\CustomForm\Fields\Type\DefaultType
{
    const AGREEMENT_BUTTON_PLACEHOLDER = '{{agreement_link}}';
    const AGREEMENT_SELECTOR = "data-role='agreement'";
    const AGREEMENT_BUTTON_SELECTOR = "data-role='agreement-open'";
    const AGREEMENT_CONTENT_SELECTOR = "data-role='agreement-content'";

    /**
     * @return string
     */
    public function getAgreementBlockHtml()
    {
        $html = str_replace(
            self::AGREEMENT_BUTTON_PLACEHOLDER,
            $this->prepareButtonHtml(),
            $this->getField()->getAgreement()
        );

        return $html;
    }

    /**
     * @return string
     */
    public function prepareButtonHtml()
    {
        return '<a title="' . $this->escapeHtmlAttr($this->getField()->getTitle()) . '" href="javascript:void(0);" '
            . self::AGREEMENT_BUTTON_SELECTOR . '>'
            . $this->escapeHtml($this->getField()->getAgreementButton())
            . '</a>';
    }

    /**
     * @return string
     */
    public function getValidation()
    {
        $validators = [];

        if ($this->_field->getRequired()) {
            $validators['required'] = true;
        }

        return !empty($validators)
            ? "data-validate='" . $this->escapeHtmlAttr($this->jsonEncode($validators)) . "'"
            : '';
    }
}
