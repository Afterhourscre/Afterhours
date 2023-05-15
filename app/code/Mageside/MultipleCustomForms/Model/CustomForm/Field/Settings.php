<?php
/**
 * Copyright © Mageside. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */
namespace Mageside\MultipleCustomForms\Model\CustomForm\Field;

class Settings
{
    const OPTION_OPTIONS_SOURCE = 'options_source';
    const OPTION_PRODUCT_ATTRIBUTE = 'product_attribute';
    const OPTION_REGION_SOURCE = 'region_source';
    const OPTION_SPECIFIC_COUNTRY = 'specific_country';
    const OPTION_SPECIFIC_FIELD = 'country_field';
    const OPTION_ALLOWED_EXTENSIONS = 'allowed_extensions';
    const OPTION_COUNT_UPLOADS = 'count_uploads';
    const OPTION_MAX_FILE_SIZE = 'max_file_size';
    const OPTION_AGREEMENT = 'agreement';
    const OPTION_AGREEMENT_BUTTON = 'agreement_button';
    const OPTION_AGREEMENT_TEXT = 'agreement_text';
    const OPTION_HIDDEN_SOURCE = 'hidden_source';
    const OPTION_HIDDEN_PRODUCT_ATTRIBUTE = 'hidden_product_attribute';
    const OPTION_HIDDEN_CATEGORY_ATTRIBUTE = 'hidden_category_attribute';
    const OPTION_HIDDEN_CUSTOMER_ATTRIBUTE = 'hidden_customer_attribute';
    const OPTION_HIDDEN_STATIC = 'hidden_static';
    const OPTION_DATE_TYPE = 'date_type';
    const OPTION_TIME_FORMAT = 'time_format';
    const OPTION_USE_CALENDAR = 'use_calendar';
    const OPTION_DATE_YEAR_FROM = 'year_from';
    const OPTION_DATE_YEAR_TO = 'year_to';
    const OPTION_TITLE = 'title';
    const OPTION_PLACEHOLDER = 'placeholder';
    const OPTION_DEFAULT_VALUE = 'default_value';
    const OPTION_COMMENT = 'comment';
    const FIELDS_WITH_OPTIONS = ['select', 'multiselect', 'radio', 'checkbox'];
    const FIELDS_WITH_MULTIPLE_OPTIONS = ['multiselect', 'checkbox'];

    /**
     * @var array
     */
    protected $_validators;

    /**
     * Settings constructor.
     * @param array $validators
     */
    public function __construct(
        $validators = []
    ) {
        $this->_validators = $validators;
    }

    /**
     * @return array
     */
    public function getAllOptions()
    {
        return [
            self::OPTION_TITLE,
            self::OPTION_PLACEHOLDER,
            self::OPTION_DEFAULT_VALUE,
            self::OPTION_COMMENT,
            self::OPTION_OPTIONS_SOURCE,
            self::OPTION_PRODUCT_ATTRIBUTE,
            self::OPTION_REGION_SOURCE,
            self::OPTION_SPECIFIC_COUNTRY,
            self::OPTION_SPECIFIC_FIELD,
            self::OPTION_ALLOWED_EXTENSIONS,
            self::OPTION_COUNT_UPLOADS,
            self::OPTION_MAX_FILE_SIZE,
            self::OPTION_AGREEMENT,
            self::OPTION_AGREEMENT_BUTTON,
            self::OPTION_AGREEMENT_TEXT,
            self::OPTION_HIDDEN_SOURCE,
            self::OPTION_HIDDEN_PRODUCT_ATTRIBUTE,
            self::OPTION_HIDDEN_CATEGORY_ATTRIBUTE,
            self::OPTION_HIDDEN_CUSTOMER_ATTRIBUTE,
            self::OPTION_HIDDEN_STATIC,
            self::OPTION_DATE_TYPE,
            self::OPTION_USE_CALENDAR,
            self::OPTION_TIME_FORMAT,
            self::OPTION_DATE_YEAR_FROM,
            self::OPTION_DATE_YEAR_TO
        ];
    }

    /**
     * @param $type
     * @return bool
     */
    public function isDataTypeArray($type)
    {
        if (in_array($type, self::FIELDS_WITH_MULTIPLE_OPTIONS)) {
            return true;
        }

        return false;
    }

    /**
     * @param $type
     * @return bool
     */
    public function hasOptionsData($type)
    {
        if (in_array($type, self::FIELDS_WITH_OPTIONS)) {
            return true;
        }

        return false;
    }

    /**
     * @return array
     */
    public function getAllowedValidators()
    {
        return $this->_validators;
    }

    /**
     * @param $type
     * @return bool
     */
    public function getValidator($type)
    {
        if (key_exists($type, $this->_validators)) {
            return $this->_validators[$type];
        }

        return false;
    }
}
