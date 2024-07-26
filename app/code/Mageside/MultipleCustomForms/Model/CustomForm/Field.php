<?php
/**
 * Copyright © Mageside. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */
namespace Mageside\MultipleCustomForms\Model\CustomForm;

use Mageside\MultipleCustomForms\Model\CustomForm\Field\Settings;
use Mageside\MultipleCustomForms\Block\Widget\CustomForm\Fields\Type\DateType;
use Magento\Framework\Validation\Validator\IsNotEmpty;

class Field extends \Magento\Framework\Model\AbstractModel
{
    const FIELD_PREFIX = 'field_';

    /**
     * @var \Mageside\MultipleCustomForms\Model\ResourceModel\CustomForm\Field\Options\CollectionFactory
     */
    protected $_optionsCollectionFactory;

    /**
     * @var \Magento\Catalog\Model\Product\Attribute\RepositoryFactory
     */
    protected $_attributeRepositoryFactory;

    /**
     * @var \Magento\Directory\Model\ResourceModel\Country\CollectionFactory
     */
    protected $_countryCollectionFactory;

    /**
     * @var \Magento\Directory\Model\ResourceModel\Region\CollectionFactory
     */
    protected $_regionCollectionFactory;

    /**
     * @var \Mageside\MultipleCustomForms\Model\ResourceModel\CustomForm\Field\OptionsFactory
     */
    protected $_resourceOptionsFactory;

    /**
     * @var Settings
     */
    protected $_fieldSettings;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Framework\App\Cache\Type\Config
     */
    protected $_configCacheType;

    /**
     * @var \Mageside\MultipleCustomForms\Helper\Config
     */
    protected $_configHelper;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $_messageManager;

    /**
     * @var \Magento\Framework\Locale\ResolverInterface
     */
    protected $_localeResolver;

    /**
     * Field constructor.
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Mageside\MultipleCustomForms\Model\ResourceModel\CustomForm\Field\Options\CollectionFactory $optionsCollectionFactory
     * @param \Magento\Catalog\Model\Product\Attribute\RepositoryFactory $attributeRepositoryFactory
     * @param \Magento\Directory\Model\ResourceModel\Country\CollectionFactory $countryCollectionFactory
     * @param \Magento\Directory\Model\ResourceModel\Region\CollectionFactory $regionCollectionFactory
     * @param \Mageside\MultipleCustomForms\Model\ResourceModel\CustomForm\Field\OptionsFactory $resourceOptionsFactory
     * @param Settings $fieldSettings
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\App\Cache\Type\Config $configCacheType
     * @param \Mageside\MultipleCustomForms\Helper\Config $configHelper
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Magento\Framework\Locale\ResolverInterface $localeResolver
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Mageside\MultipleCustomForms\Model\ResourceModel\CustomForm\Field\Options\CollectionFactory $optionsCollectionFactory,
        \Magento\Catalog\Model\Product\Attribute\RepositoryFactory $attributeRepositoryFactory,
        \Magento\Directory\Model\ResourceModel\Country\CollectionFactory $countryCollectionFactory,
        \Magento\Directory\Model\ResourceModel\Region\CollectionFactory $regionCollectionFactory,
        \Mageside\MultipleCustomForms\Model\ResourceModel\CustomForm\Field\OptionsFactory $resourceOptionsFactory,
        \Mageside\MultipleCustomForms\Model\CustomForm\Field\Settings $fieldSettings,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Cache\Type\Config $configCacheType,
        \Mageside\MultipleCustomForms\Helper\Config $configHelper,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\Locale\ResolverInterface $localeResolver,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->_optionsCollectionFactory = $optionsCollectionFactory;
        $this->_attributeRepositoryFactory = $attributeRepositoryFactory;
        $this->_countryCollectionFactory = $countryCollectionFactory;
        $this->_regionCollectionFactory = $regionCollectionFactory;
        $this->_resourceOptionsFactory = $resourceOptionsFactory;
        $this->_fieldSettings = $fieldSettings;
        $this->_storeManager = $storeManager;
        $this->_configCacheType = $configCacheType;
        $this->_configHelper = $configHelper;
        $this->_messageManager = $messageManager;
        $this->_localeResolver = $localeResolver;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    protected function _construct()
    {
        $this->_init('Mageside\MultipleCustomForms\Model\ResourceModel\CustomForm\Field');
    }

    /**
     * @return null|string
     */
    public function getBackendType()
    {
        $suffix = null;
        switch ($this->getType()) {
            case 'agreement':
            case 'input':
                $suffix = 'varchar';
                break;
            case 'date':
            case 'textarea':
            case 'multiselect':
            case 'file':
            case 'hidden':
            case 'checkbox':
                $suffix = 'text';
                break;
            case 'select':
                if ($this->getOptionsSource() == 'country') {
                    $suffix = 'varchar';
                } else {
                    $suffix = 'integer';
                }
                break;
            case 'radio':
                if ($this->getOptionsSource() == 'country') {
                    $suffix = 'varchar';
                } else {
                    $suffix = 'integer';
                }
                break;
        }

        return $suffix;
    }

    /**
     * @return array|mixed
     */
    public function getDefaultValue()
    {
        if ($this->_fieldSettings->isDataTypeArray($this->getType())) {
            return $this->getData('default_value') ? explode(',', $this->getData('default_value')) : [];
        }

        return $this->getData('default_value');
    }

    /**
     * @param bool $addFirstEmpty
     * @param bool $raw
     * @param bool $keyAsId
     * @return array|mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getOptions($addFirstEmpty = true, $raw = false, $keyAsId = true)
    {
        if ($raw) {
            return $this->getData('options');
        }

        if (!$this->_fieldSettings->hasOptionsData($this->getType())) {
            return [];
        }

        $options = [];
        $source = $this->getData(Settings::OPTION_OPTIONS_SOURCE);
        $regionSource = $this->getData(Settings::OPTION_REGION_SOURCE);
        $countryCode = $this->getData(Settings::OPTION_SPECIFIC_COUNTRY);

        switch ($source) {
            case 'custom':
                $optionsRaw = $this->_optionsCollectionFactory->create();
                $optionsRaw->addFieldToFilter('field_id', $this->getId());
                if (!empty($optionsRaw)) {
                    foreach ($optionsRaw->getData() as $option) {
                        if ($keyAsId) {
                            $options[$option['id']] = [
                                'value' => $option['id'],
                                'label' => $option['label']
                            ];
                        } else {
                            $options[] = [
                                'id'    => $option['id'],
                                'label' => $option['label']
                            ];
                        }
                    }
                }
                break;
            case 'product_attribute':
                $cacheKey = 'MAGESIDE_FORM_SELECT_product_attribute_'
                    . $this->getData(Settings::OPTION_PRODUCT_ATTRIBUTE)
                    . '_' . $this->_storeManager->getStore()->getCode();
                $cache = $this->_configCacheType->load($cacheKey);
                if ($cache) {
                    $options = unserialize($cache);
                } else {
                    $optionsRaw = $this->_attributeRepositoryFactory
                        ->create()
                        ->get($this->getData(Settings::OPTION_PRODUCT_ATTRIBUTE))
                        ->getOptions();
                    unset($optionsRaw[0]);
                    if (!empty($optionsRaw)) {
                        foreach ($optionsRaw as $option) {
                            $options[$option->getValue()] = [
                                'value' => $option->getValue(),
                                'label' => $option->getLabel()
                            ];
                        }
                    }
                    $this->_configCacheType->save(serialize($options), $cacheKey);
                }
                break;
            case 'country':
                $cacheKey = 'MAGESIDE_FORM_SELECT_country_' . $this->_storeManager->getStore()->getCode();
                $cache = $this->_configCacheType->load($cacheKey);
                if ($cache) {
                    $options = unserialize($cache);
                } else {
                    $optionsRaw = $this->_countryCollectionFactory
                        ->create()
                        ->toOptionArray();
                    unset($optionsRaw[0]);
                    foreach ($optionsRaw as $option) {
                        $options[$option['value']] = [
                            'value' => $option['value'],
                            'label' => $option['label']
                        ];
                    }
                    $this->_configCacheType->save(serialize($options), $cacheKey);
                }
                break;
            case 'region':
                $specificCountry = ($regionSource == 'specific_country' && !empty($countryCode)) ? $countryCode : '';
                $cacheKey = 'MAGESIDE_FORM_SELECT_region_'
                    . $specificCountry
                    . '_' . $this->_storeManager->getStore()->getCode();
                $cache = $this->_configCacheType->load($cacheKey);
                if ($cache) {
                    $options = unserialize($cache);
                } else {
                    $optionsCollection = $this->_regionCollectionFactory->create();
                    if ($regionSource == 'specific_country' && !empty($countryCode)) {
                        $optionsCollection->addCountryFilter($countryCode);
                    }
                    $optionsRaw = $optionsCollection->toOptionArray();
                    unset($optionsRaw[0]);
                    foreach ($optionsRaw as $option) {
                        $options[$option['value']] = [
                            'value' => $option['value'],
                            'label' => $option['label']
                        ];
                    }
                    $this->_configCacheType->save(serialize($options), $cacheKey);
                }
                break;
        }

        if ($addFirstEmpty) {
            $options = array_merge(
                ['00' =>
                    [
                        'value' => '',
                        'label' => ' '
                    ]
                ],
                $options
            );
        }

        return $options;
    }

    /**
     * @param $value
     * @return array|string
     */
    public function getFieldOutput($value)
    {
        if ($this->_fieldSettings->hasOptionsData($this->getType())) {
            $options = $this->getOptions(false);
            if ($this->_fieldSettings->isDataTypeArray($this->getType())) {
                $dataOption = [];
                $value = !is_array($value) ? explode(',', $value) : $value;
                foreach ($value as $option) {
                    $dataOption[] = !empty($options[$option]['label']) ? $options[$option]['label'] : $option;
                }
                return implode(', ', $dataOption);
            } else {
                return !empty($options[$value]['label']) ? $options[$value]['label'] : $value;
            }
        } else {
            return $value;
        }
    }

    /**
     * @param $submissionId
     * @return array|string
     */
    public function getSubmissionData($submissionId)
    {
        $value = $this->getResource()->loadSubmissionData($submissionId, $this);

        return $this->getFieldOutput($value);
    }

    /**
     * @param $data
     * @return bool
     */
    public function getSubmittedValue($data)
    {
        $name = self::FIELD_PREFIX . $this->getId();
        if ($this->getOptionsSource() == 'region' && !empty($data['region_' . $this->getId()])) {
            $value = $data['region_' . $this->getId()];
        } elseif ($this->getType() == 'date' && !empty($data[$name])) {
            $value = $this->prepareDateValue($data[$name]);
        } elseif (!empty($data[$name])) {
            $value = $data[$name];
        } else {
            return false;
        }

        return $value;
    }

    /**
     * @param $value
     * @return string
     */
    protected function prepareDateValue($value)
    {
        $value['date'] = isset($value['date']) ? $value['date'] : '';
        $value['hour'] = isset($value['hour']) ? DateType::getValueWithLeadingZeros($value['hour']) : '';
        $value['minute'] = isset($value['minute']) ? DateType::getValueWithLeadingZeros($value['minute']) : '';
        $value['day_part'] = isset($value['day_part']) ? $value['day_part'] : '';

        if (empty($value['date'])) {
            $value['d'] = isset($value['day']) ? DateType::getValueWithLeadingZeros($value['day']) : '';
            $value['M'] = isset($value['month']) ? DateType::getValueWithLeadingZeros($value['month']) : '';
            $value['yy'] = isset($value['year']) ? $value['year'] : '';
            $dateFormat = $this->_configHelper->getDateFormat();
            $value['date'] = strtr($dateFormat, $value);
        }

        $datePartFormat = '';
        $timePartFormat = '';
        if ($this->getDateType() == 'date' || $this->getDateType() == 'date_time') {
            $datePartFormat = 'date';
        }
        if ($this->getDateType() == 'time' || $this->getDateType() == 'date_time') {
            $connector = !empty($value['day_part']) ? ' ' : '';
            $timePartFormat = 'hour:minute' . $connector . 'day_part';
        }
        $connector = ($datePartFormat && $timePartFormat) ? ' ' : '';
        $format = $datePartFormat . $connector . $timePartFormat;

        return strtr($format, $value);
    }

    /**
     * @param $data
     * @return bool
     */
    public function validateData($data)
    {
        try {
            $valid = true;

            $value = $this->getSubmittedValue($data);
	    $value = is_string($value) ? trim($value) : $value;

            if (empty($value)) {
                if ($this->getRequired()) {
                    $this->_messageManager->addErrorMessage(__('%1 is required.', $this->getTitle()));
                    return false;
                } else {
                    return true;
                }
            }
            if ($validators = $this->getValidation()) {
                if (!is_array($validators)) {
                    $validators = [$validators];
                }
                foreach ($validators as $type) {
                    if (!$validator = $this->_fieldSettings->getValidator($type)) {
                        if (isset($validator['validator'])) {
                            $valid = false;
                            continue;
                        }
                    }
                    $validator = $validator['validator'];

                    $arguments = !empty($validator['arguments']) ? $validator['arguments'] : [];
                    foreach ($arguments as $key => $argument) {
                        if ($key === 'locale') {
                            $arguments[$key] = $this->_localeResolver->getLocale();
                        }
                    }

                    if ($type == 'emails') {
                        $emails = explode(',', $value);
                        foreach ($emails as $email) {
                            if (!\Zend_Validate::is(trim($email), $validator['class'], $arguments)) {
                                $valid = false;
                                $this->_messageManager->addErrorMessage(__($validator['errorMessage'], $this->getTitle()));
                            }
                        }
                    } else {
                        if (!\Zend_Validate::is($value, $validator['class'], $arguments)) {
                            $valid = false;
                            $this->_messageManager->addErrorMessage(__($validator['errorMessage'], $this->getTitle()));
                        }
                    }
                }
            }

            return $valid;
        } catch (\Exception $e) {
            $this->_messageManager->addErrorMessage(__('Unable to validate field %1.', $this->getTitle()));

            return false;
        }
    }
}
