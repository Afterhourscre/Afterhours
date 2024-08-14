<?php
/**
 * Copyright © Mageside. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */
namespace Mageside\MultipleCustomForms\Block\Widget\CustomForm\Fields\Type;

class CountryType extends \Mageside\MultipleCustomForms\Block\Widget\CustomForm\Fields\Type\SelectType
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
     * CountryType constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Directory\Block\Data $directoryBlock
     * @param \Magento\Framework\App\Cache\Type\Config $configCacheType
     * @param \Mageside\MultipleCustomForms\Model\CustomForm\Field\Settings $fieldSettings
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
     * @return string
     */
    public function getDefaultValue()
    {
        $defValue = $this->_field->getDefaultValue();
        if (empty($defValue)) {
            $defValue = $this->_directoryBlock->getCountryId();
        }

        return $defValue;
    }
}
