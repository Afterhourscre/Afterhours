<?php
/**
 * Copyright © Mageside. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */
namespace Mageside\MultipleCustomForms\Block\Widget\CustomForm\Fields\Type;

use Mageside\MultipleCustomForms\Model\CustomForm\Field;

class RegionType extends \Mageside\MultipleCustomForms\Block\Widget\CustomForm\Fields\Type\SelectType
{
    /**
     * @var \Magento\Framework\App\Cache\Type\Config
     */
    protected $_configCacheType;

    /**
     * @var \Magento\Directory\Block\Data
     */
    protected $_directoryBlock;

    /**
     * RegionType constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Directory\Block\Data $directoryBlock
     * @param \Magento\Framework\App\Cache\Type\Config $configCacheType
     * @param Field\Settings $fieldSettings
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Directory\Block\Data $directoryBlock,
        \Magento\Framework\App\Cache\Type\Config $configCacheType,
        \Mageside\MultipleCustomForms\Model\CustomForm\Field\Settings $fieldSettings,
        array $data = []
    ) {
        $this->_directoryBlock = $directoryBlock;
        $this->_configCacheType = $configCacheType;
        parent::__construct($context, $fieldSettings, $data);
    }

    /**
     * @return array|mixed
     */
    public function getOptions()
    {
        $options = [
            [
                'value' => '',
                'label' => 'Please select a region, state or province.',
            ]
        ];

        return $options;
    }

    /**
     * @return string
     */
    public function getDefaultValue()
    {
        return $this->_field->getData('default_value');
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getCountrySelectHtml()
    {
        $html = '';
        $field = $this->getField();
        if ($field->getRegionSource() == 'specific_country') {
            $countryFieldId = $this->getCountryFieldId();
            $html = $this->getLayout()->createBlock(
                'Magento\Framework\View\Element\Html\Select'
            )->setName(
                $this->escapeHtmlAttr($countryFieldId)
            )->setId(
                $this->escapeHtmlAttr($countryFieldId)
            )->setTitle(
                __('Country')
            )->setValue(
                $this->escapeHtmlAttr($field->getSpecificCountry())
            )->setOptions(
                [
                    [
                        'value' => $field->getSpecificCountry(),
                        'label' => $field->getSpecificCountry()
                    ]
                ]
            )->setExtraParams(
                'style="display:none;"'
            )->getHtml();
        }

        return $html;
    }

    /**
     * @return string
     */
    public function getCountryFieldId()
    {
        $field = $this->getField();
        if ($field->getRegionSource() == 'specific_country') {
            return $countryFieldId = 'country_' . $field->getId();
        }

        return $countryFieldId = Field::FIELD_PREFIX . $field->getCountryField();
    }
}
