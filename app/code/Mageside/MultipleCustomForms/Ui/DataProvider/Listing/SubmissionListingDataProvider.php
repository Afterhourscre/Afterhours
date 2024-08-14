<?php
/**
 * Copyright © Mageside. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */
namespace Mageside\MultipleCustomForms\Ui\DataProvider\Listing;

use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\ReportingInterface;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider;

class SubmissionListingDataProvider extends DataProvider
{
    /**
     * @var \Mageside\MultipleCustomForms\Model\CustomForm
     */
    protected $_customForm;

    /**
     * @var \Mageside\MultipleCustomForms\Model\CustomForm\Field\Settings
     */
    protected $_fieldSettings;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * SubmissionListingDataProvider constructor.
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param ReportingInterface $reporting
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param RequestInterface $request
     * @param FilterBuilder $filterBuilder
     * @param \Mageside\MultipleCustomForms\Model\CustomForm $customForm
     * @param \Mageside\MultipleCustomForms\Model\CustomForm\Field\Settings $fieldSettings
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        ReportingInterface $reporting,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        RequestInterface $request,
        FilterBuilder $filterBuilder,
        \Mageside\MultipleCustomForms\Model\CustomForm $customForm,
        \Mageside\MultipleCustomForms\Model\CustomForm\Field\Settings $fieldSettings,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        array $meta = [],
        array $data = []
    ) {
        $this->_customForm = $customForm;
        $this->_fieldSettings = $fieldSettings;
        $this->storeManager = $storeManager;
        parent::__construct(
            $name,
            $primaryFieldName,
            $requestFieldName,
            $reporting,
            $searchCriteriaBuilder,
            $request,
            $filterBuilder,
            $meta,
            $data
        );

        $store = $this->storeManager->getStore(0);
        $this->storeManager->setCurrentStore($store->getCode());
    }

    /**
     * @return array
     */
    public function getMeta()
    {
        $meta = $this->meta;

        $form = $this->getForm();
        $columns = [];
        if ($form->getId()) {
            $fieldCollection = $form->getFieldCollection();
            $sortOrder = 20;
            foreach ($fieldCollection->getItems() as $field) {
                if ((bool)$field['show_in_grid']) {
                    if ($this->_fieldSettings->hasOptionsData($field->getType())) {
                        $columns['field' . $field->getId()] = [
                            'arguments' => [
                                'data' => [
                                    'options' => $field->getOptions(),
                                    'config' => [
                                        'componentType' => 'column',
                                        'label' => $field->getTitle(),
                                        'dataType' => 'select',
                                        'component' => 'Magento_Ui/js/grid/columns/select',
                                        'sortOrder' => $sortOrder + 5
                                    ]
                                ]
                            ],
                        ];
                        if (in_array($field->getType(), ["select", "radio"])) {
                            $columns['field' . $field->getId()]['arguments']['data']['config']['filter'] = 'select';
                        }
                    } else {
                        $columns['field' . $field->getId()] = [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'componentType' => 'column',
                                        'label' => $field->getTitle(),
                                        'filter' => 'text',
                                        'sortOrder' => $sortOrder + 5
                                    ]
                                ]
                            ],
                        ];
                    }
                }
            }

            $meta = array_replace_recursive(
                $meta,
                [
                    'submission_columns' => [
                        'children' => $columns,
                    ]
                ]
            );
        }

        return $meta;
    }

    /**
     * @return array
     */
    public function getData()
    {
        if ($formId = $this->request->getParam('id')) {
            $this->data['config']['params']['id'] = $formId;

            $this->addFilter(
                $this->filterBuilder->setField('main_table.form_id')
                    ->setValue($formId)
                    ->create()
            );
        }
        $data = parent::getData();

        return $data;
    }

    public function getSearchResult()
    {
        $collection = parent::getSearchResult();
        if (!$collection->isLoaded()) {
            $form = $this->getForm();
            if ($form->getId()) {
                $fieldCollection = $form->getFieldCollection();
                foreach ($fieldCollection->getItems() as $field) {
                    if ((bool)$field['show_in_grid']) {
                        $tableName = $collection->getTable('ms_cf_submission') . '_' . $field->getBackendType();
                        $aliasTable = 'field' . $field->getId();
                        $collection->getSelect()->joinLeft(
                            [$aliasTable => $tableName],
                            $aliasTable . '.submission_id = main_table.id AND ' . $aliasTable . '.field_id = \'' . $field->getId() . '\'',
                            ['field' . $field->getId() => 'value']
                        );
                    }
                }
            }
        }

        return $collection;
    }

    protected function getForm()
    {
        if ($formId = $this->request->getParam('id')) {
            if (!$this->_customForm->getId()) {
                $this->_customForm->load($formId);
            }
        }

        return $this->_customForm;
    }
}
