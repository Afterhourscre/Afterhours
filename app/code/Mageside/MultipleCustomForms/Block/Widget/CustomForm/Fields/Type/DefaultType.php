<?php
/**
 * Copyright © Mageside. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */
namespace Mageside\MultipleCustomForms\Block\Widget\CustomForm\Fields\Type;

class DefaultType extends \Mageside\MultipleCustomForms\Block\Widget\AbstractBlock
{
    /**
     * @var \Mageside\MultipleCustomForms\Model\CustomForm\Field
     */
    protected $_field;

    /**
     * @var array
     */
    protected $_validators = [];

    /**
     * @var \Mageside\MultipleCustomForms\Model\CustomForm\Field\Settings
     */
    protected $_fieldSettings;

    /**
     * DefaultType constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Mageside\MultipleCustomForms\Model\CustomForm\Field\Settings $fieldSettings
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Mageside\MultipleCustomForms\Model\CustomForm\Field\Settings $fieldSettings,
        array $data = []
    ) {
        $this->_fieldSettings = $fieldSettings;
        $this->_validators = $fieldSettings->getAllowedValidators();

        parent::__construct($context, $data);
    }

    /**
     * @param $field
     * @return $this
     */
    public function setField(\Mageside\MultipleCustomForms\Model\CustomForm\Field $field)
    {
        $this->_field = $field;

        return $this;
    }

    /**
     * @return \Mageside\MultipleCustomForms\Model\CustomForm\Field
     */
    public function getField()
    {
        return $this->_field;
    }

    /**
     * @param null $additional
     * @return string
     */
    public function getFieldHtmlId($additional = null)
    {
        if ($additional) {
            $additional = '_' . $additional;
        }

        return \Mageside\MultipleCustomForms\Model\CustomForm\Field::FIELD_PREFIX . $this->_field->getId() . $additional;
    }

    /**
     * @return string
     */
    public function getFieldHtmlName()
    {
        if ($this->_fieldSettings->isDataTypeArray($this->getField()->getType())) {
            return $this->getFieldHtmlId() . '[]';
        }

        return $this->getFieldHtmlId();
    }

    /**
     * @return string
     */
    public function getFormHtmlId()
    {
        return \Mageside\MultipleCustomForms\Model\CustomForm::FORM_PREFIX . $this->_field->getFormId();
    }

    /**
     * @return string
     */
    public function getDefaultValue()
    {
        return $this->_field->getDefaultValue() ? : '';
    }

    /**
     * @param $option
     * @return bool
     */
    public function isOptionSelected($option)
    {
        $defaultValue = $this->getDefaultValue();
        if (empty($defaultValue)) {
            return false;
        }

        if (!is_array($defaultValue)) {
            $defaultValue = [$defaultValue];
        }

        if (array_search($option, $defaultValue) !== false) {
            return true;
        }

        return false;
    }

    /**
     * @return string
     */
    public function getValidation()
    {
        $validators = [];

        if ($this->isRequired()) {
            $validators['required'] = true;
        }

        if (!empty($this->_field->getValidation())) {
            foreach (explode(',', $this->_field->getValidation()) as $validator) {
                if (key_exists($validator, $this->_validators)) {
                    foreach ($this->_validators[$validator]['condition'] as $key => $value) {
                        $validators[$key] = $value;
                    }
                }
            }
        }

        return !empty($validators)
            ? "data-validate='" . $this->escapeHtmlAttr($this->jsonEncode($validators)) . "'"
            : '';
    }

    /**
     * @return bool
     */
    public function isRequired()
    {
        return (bool) $this->_field->getRequired();
    }

    /**
     * @return mixed
     */
    public function getComment()
    {
        return $this->_field->getComment();
    }
}
