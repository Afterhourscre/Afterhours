<?php
/**
 * Copyright © Mageside. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */
namespace Mageside\MultipleCustomForms\Block\Widget\CustomForm\Fields\Type;

use Mageside\MultipleCustomForms\Model\CustomForm\Field\Settings;

class DateType extends \Mageside\MultipleCustomForms\Block\Widget\CustomForm\Fields\Type\DefaultType
{
    /**
     * @var \Mageside\MultipleCustomForms\Helper\Config
     */
    protected $_configHelper;

    /**
     * DateType constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param Settings $fieldSettings
     * @param \Mageside\MultipleCustomForms\Helper\Config $configHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Mageside\MultipleCustomForms\Model\CustomForm\Field\Settings $fieldSettings,
        \Mageside\MultipleCustomForms\Helper\Config $configHelper,
        array $data = []
    ) {
        $this->_configHelper = $configHelper;
        parent::__construct($context, $fieldSettings, $data);
    }

    /**
     * @return mixed|string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getDateHtml()
    {
        if ($this->getField()->getUseCalendar()) {
            return $this->getCalendarDateHtml();
        }

        return $this->getDropDownsDateHtml();
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getTimeHtml()
    {
        if ($this->getField()->getTimeFormat()) {
            $hourStart = 0;
            $hourEnd = 23;
            $dayPartHtml = '';
        } else {
            $hourStart = 1;
            $hourEnd = 12;
            $dayPartHtml = $this->_getHtmlSelect(
                'day_part'
            )->setOptions(
                ['am' => __('AM'), 'pm' => __('PM')]
            )->getHtml();
        }
        $hoursHtml = $this->_getSelectFromToHtml('hour', $hourStart, $hourEnd);
        $minutesHtml = $this->_getSelectFromToHtml('minute', 0, 59);

        return $hoursHtml . '&nbsp;<b>:</b>&nbsp;' . $minutesHtml . '&nbsp;' . $dayPartHtml;
    }

    /**
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getCalendarDateHtml()
    {
        $field = $this->getField();

        $escapedDateFormat = $this->_configHelper->getDateFormat();
        $calendar = $this->getLayout()->createBlock(
            \Magento\Framework\View\Element\Html\Date::class
        )->setId(
            'options_' . $this->escapeHtmlAttr($this->getFieldHtmlId()) . '_date'
        )->setName(
            $this->escapeHtmlAttr($this->getFieldHtmlName()) . '[date]'
        )->setClass(
            'datetime-picker input-text'
        )->setImage(
            $this->getViewFileUrl('Magento_Theme::calendar.png')
        )->setDateFormat(
            $escapedDateFormat
        )->setValue(
            $this->getDefaultValueByPart('date')
        )->setYearsRange(
            $field->getYearFrom() . ':' . $field->getYearTo()
        );

        return $calendar->getHtml();
    }

    /**
     * @param $part
     * @return null|string
     */
    public function getDefaultValueByPart($part)
    {
        $result = null;

        if ($default = $this->getDefaultValue()) {
            $field = $this->getField();

            /**
             * Get date format and convert from ISO to PHP format
             */
            $dateFormat = $this->_configHelper->getDateFormat();
            $dateFormat = strtr($dateFormat, ['M'=>'n', 'd'=>'j', 'yy'=>'Y']);

            $date = new \DateTime($default);

            $formatters = [
                'date' => $dateFormat,
                'day' => 'j',
                'month' => 'n',
                'year' => 'Y',
                'hour12' => 'g',
                'hour24' => 'G',
                'minute' => 'i',
                'day_part' => 'a'
            ];

            if ($part == 'hour') {
                $part = $field->getData(Settings::OPTION_TIME_FORMAT) ? 'hour24' : 'hour12';
            }

            if (isset($formatters[$part])) {
                $format = $formatters[$part];
                $result = $date->format($format);
            }
        }

        return $result;
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getDropDownsDateHtml()
    {
        $field = $this->getField();

        $fieldsSeparator = '&nbsp;';
        $fieldsOrder = $this->_configHelper->getDateFormat();
        $fieldsOrder = str_replace('/', $fieldsSeparator, $fieldsOrder);

        $monthsHtml = $this->_getSelectFromToHtml('month', 1, 12);
        $daysHtml = $this->_getSelectFromToHtml('day', 1, 31);
        $yearsHtml = $this->_getSelectFromToHtml('year', $field->getYearFrom(), $field->getYearTo());

        $translations = ['d' => $daysHtml, 'M' => $monthsHtml, 'yy' => $yearsHtml];

        return strtr($fieldsOrder, $translations);
    }

    /**
     * @param $name
     * @param $from
     * @param $to
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _getSelectFromToHtml($name, $from, $to)
    {
        $options = [['value' => '', 'label' => '-']];
        for ($i = $from; $i <= $to; $i++) {
            $options[] = ['value' => $i, 'label' => $this->getValueWithLeadingZeros($i)];
        }
        return $this->_getHtmlSelect($name)->setOptions($options)->getHtml();
    }

    /**
     * @param $name
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _getHtmlSelect($name)
    {
        $field = $this->getField();
        $value = $this->getDefaultValueByPart($name);

        $require = '';
        $select = $this->getLayout()->createBlock(
            \Magento\Framework\View\Element\Html\Select::class
        )->setId(
            'options_' . $this->escapeHtmlAttr($this->getFieldHtmlId()) . '_' . $name
        )->setClass(
            'product-custom-option admin__control-select datetime-picker' . $require
        )->setExtraParams()->setName(
            $this->escapeHtmlAttr($this->getFieldHtmlName()) . '[' . $name . ']'
        );

        $extraParams = 'style="width:auto"';
        $extraParams .= ' data-role="calendar-dropdown" data-calendar-role="' . $name . '"';
        $extraParams .= ' data-selector="' . $this->escapeHtmlAttr($select->getName()) . '"';
        if ($field->getRequired()) {
            $extraParams .= ' data-validate=\'{"datetime-validation": true}\'';
        }

        $select->setExtraParams($extraParams);

        if ($value !== null) {
            $select->setValue($this->escapeHtmlAttr($value));
        }

        return $select;
    }

    /**
     * @param $value
     * @return string
     */
    public static function getValueWithLeadingZeros($value)
    {
        return $value < 10 ? '0' . $value : $value;
    }
}
