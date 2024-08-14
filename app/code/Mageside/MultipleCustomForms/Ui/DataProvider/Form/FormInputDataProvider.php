<?php
/**
 * Copyright © Mageside. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */
namespace Mageside\MultipleCustomForms\Ui\DataProvider\Form;

use Magento\Ui\DataProvider\Modifier\ModifierInterface;
use Magento\Ui\DataProvider\Modifier\PoolInterface;
use Mageside\MultipleCustomForms\Model\CustomForm\Field\Settings;

class FormInputDataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * @var PoolInterface
     */
    private $_pool;

    /**
     * @var null|\Mageside\MultipleCustomForms\Model\CustomForm\Field
     */
    private $_field = null;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private $request;

    /**
     * @var \Mageside\MultipleCustomForms\Model\CustomForm\FieldFactory
     */
    private $_fieldFactory;

    /**
     * @var \Mageside\MultipleCustomForms\Helper\Config
     */
    private $_configHelper;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * FormInputDataProvider constructor.
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param \Magento\Framework\App\RequestInterface $request
     * @param PoolInterface $pool
     * @param \Mageside\MultipleCustomForms\Model\CustomForm\FieldFactory $fieldFactory
     * @param \Mageside\MultipleCustomForms\Helper\Config $configHelper
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        \Magento\Framework\App\RequestInterface $request,
        PoolInterface $pool,
        \Mageside\MultipleCustomForms\Model\CustomForm\FieldFactory $fieldFactory,
        \Mageside\MultipleCustomForms\Helper\Config $configHelper,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);

        $this->request = $request;
        $this->_pool = $pool;
        $this->_fieldFactory = $fieldFactory;
        $this->_configHelper = $configHelper;
        $this->storeManager = $storeManager;

        $storeId = (int) $this->request->getParam('store', 0);
        $store = $this->storeManager->getStore($storeId);
        $this->storeManager->setCurrentStore($store->getCode());
    }

    public function getMeta()
    {
        $meta = parent::getMeta();
        /** @var ModifierInterface $modifier */
        foreach ($this->_pool->getModifiersInstances() as $modifier) {
            $meta = $modifier->modifyMeta($meta);
        }

        $options = [];
        $dateFormat = $this->_configHelper->getDateFormat();
        $options['inputDateFormat'] = $dateFormat;
        $options['outputDateFormat'] = $dateFormat;
        $options['showsDate'] = true;
        $options['showsTime'] = true;

        $field = $this->getField();
        if ($field->getId()) {
            if ($field->getData(Settings::OPTION_TIME_FORMAT) == '1') {
                $options['timeFormat'] = 'HH:mm';
            }
        }

        if (!empty($options)) {
            $meta['fields']['children']['default_value_date']['arguments']['data']['config']['options'] = $options;
        }

        $this->meta = $meta;

        if (!empty($field->getData('useDefault')) && $this->request->getParam('store')) {
            foreach ($field->getData('useDefault') as $title => $usedDefault) {
                $this->titleUsedDefault($title, $usedDefault);
            }
        }

        return $this->meta;
    }

    /**
     * @param $titleIndex
     * @param $usedDefault
     * @return $this
     */
    protected function titleUsedDefault($titleIndex, $usedDefault)
    {
        $useDefaultConfig = [
            'usedDefault'   => $usedDefault,
            'disabled'      => $usedDefault,
            'service'       => [
                'template'  => 'ui/form/element/helper/service',
            ]
        ];
        $this->meta['fields']['children'][$titleIndex]['arguments']['data']['config'] = $useDefaultConfig;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        $formId = $this->request->getParam('form_id');
        if ($formId !== null) {
            $this->data['config']['data']['field']['form_id'] = (int)$formId;
        }

        $field = $this->getField();
        if ($field->getId()) {
            $this->data['config']['data']['field'] = $field->toArray();
        }

        $this->data['config']['data']['field']['store_id'] = (int) $this->storeManager->getStore()->getId();

        /** @var ModifierInterface $modifier */
        foreach ($this->_pool->getModifiersInstances() as $modifier) {
            $this->data = $modifier->modifyData($this->data);
        }

        return $this->data;
    }

    protected function getField()
    {
        if ($this->_field === null) {
            /** @var \Mageside\MultipleCustomForms\Model\CustomForm\Field $field */
            $field = $this->_fieldFactory->create();
            if ($fieldId = $this->request->getParam('record_id')) {
                $field->load((int)$fieldId, 'id');

                if (!empty($field->getValidation())) {
                    $field->setValidation(explode(',', $field->getValidation()));
                }

                if ($field->getOptionsSource() == 'custom') {
                    $options = $field->getOptions(false, false, false);
                    if (!empty($options)) {
                        $field->setOptions($options);
                    }
                }

                $type = $field->getType();
                if ($defaultValue = $field->getData('default_value')) {
                    if ($type == 'select' || $type == 'radio') {
                        $field->setData('default_value_select', $defaultValue);
                    } elseif ($type == 'checkbox' || $type == 'multiselect') {
                        $field->setData('default_value_multiselect', explode(',', $defaultValue));
                    } elseif ($type == 'date') {
                        $field->setData('default_value_date', $defaultValue);
                    }
                }
            }
            $this->_field = $field;
        }

        return $this->_field;
    }

    /**
     * @inheritdoc
     */
    public function addFilter(\Magento\Framework\Api\Filter $filter)
    {
    }
}
