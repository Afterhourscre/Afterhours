<?php
/**
 * Copyright © Mageside. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */
namespace Mageside\MultipleCustomForms\Ui\DataProvider\Form;

class DependencyInputDataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private $request;

    /**
     * @var \Mageside\MultipleCustomForms\Model\CustomForm\Field\Settings
     */
    protected $_fieldSettings;

    /**
     * @var \Mageside\MultipleCustomForms\Model\ResourceModel\CustomForm\Field\CollectionFactory
     */
    protected $fieldCollectionFactory;

    protected $dependencyCollectionFactory;

    /**
     * DependencyInputDataProvider constructor.
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Mageside\MultipleCustomForms\Model\CustomForm\Field\Settings $fieldSettings
     * @param \Mageside\MultipleCustomForms\Model\ResourceModel\CustomForm\Field\CollectionFactory $fieldCollectionFactory
     * @param \Mageside\MultipleCustomForms\Model\ResourceModel\RecipientDependency\CollectionFactory $dependencyCollectionFactory
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        \Magento\Framework\App\RequestInterface $request,
        \Mageside\MultipleCustomForms\Model\CustomForm\Field\Settings $fieldSettings,
        \Mageside\MultipleCustomForms\Model\ResourceModel\CustomForm\Field\CollectionFactory $fieldCollectionFactory,
        \Mageside\MultipleCustomForms\Model\ResourceModel\RecipientDependency\CollectionFactory $dependencyCollectionFactory,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->request = $request;
        $this->_fieldSettings = $fieldSettings;
        $this->fieldCollectionFactory = $fieldCollectionFactory;
        $this->dependencyCollectionFactory = $dependencyCollectionFactory;
    }

    /**
     * @return array
     */
    public function getMeta()
    {
        $meta = parent::getMeta();

        $formId = $this->request->getParam('form_id');
        $dataFields = $this->fieldCollectionFactory->create()
            ->addFieldToFilter('form_id', $formId)
            ->addOrderByPosition()
            ->addOptionsData();


        $fields = [];
        foreach ($dataFields as $filter) {
            if (!$this->_fieldSettings->hasOptionsData($filter->getType())) {
                continue;
            }
            $fields['field_' . $filter->getId()] = [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'label'         => $filter->getTitle(),
                            'componentType' => 'field',
                            'options'       => $filter->getOptions(),
                            'component'     => 'Mageside_MultipleCustomForms/js/components/form/multiselect',
                            'formElement'   => 'input',
                            'dataScope'     => 'data.fields.field_' . $filter->getId()
                        ]
                    ]
                ]
            ];
        }

        $meta = array_replace_recursive(
            $meta,
            [
                'fields' => [
                    'children' => $fields,
                ]
            ]
        );

        return $meta;
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        $recordId = $this->request->getParam('record_id');

        if (isset($recordId)) {
            $this->data['config']['data']['record_id'] = $recordId;
        }

        $fieldId = $this->request->getParam('field_id');
        if (isset($fieldId)) {
            $this->data['config']['data']['field_id'] = $fieldId;
            $this->data['config']['data']['fields'] = $this->prepareData($fieldId);
        }

        return $this->data;
    }

    /**
     * @inheritdoc
     */
    public function addFilter(\Magento\Framework\Api\Filter $filter)
    {
    }

    public function prepareData($fieldId)
    {
        $dependencyCollection = $this->dependencyCollectionFactory->create()
            ->addFieldToFilter('recipient_id', $fieldId)
            ->getItems();
        $dependencyData = [];
        if (!empty($dependencyCollection)) {
            foreach ($dependencyCollection as $item) {
                $value = explode(',', $item->getValue());
                $dependencyData['field_' . $item->getFieldId()] = $value;
            }
        }

        if (!empty($dependencyData)) {
            return $dependencyData;
        } else {
            return $this;
        }
    }
}
