<?php
/**
 * Copyright © Mageside. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */
namespace Mageside\MultipleCustomForms\Model\Source;

class CountryFields implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var \Mageside\MultipleCustomForms\Model\ResourceModel\CustomForm\Field\Collection
     */
    private $_fieldCollection;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private $_request;

    /**
     * @var array
     */
    private $_options;

    /**
     * Fields constructor.
     * @param \Mageside\MultipleCustomForms\Model\ResourceModel\CustomForm\Field\Collection $fieldCollection
     */
    public function __construct(
        \Mageside\MultipleCustomForms\Model\ResourceModel\CustomForm\Field\Collection $fieldCollection,
        \Magento\Framework\App\RequestInterface $request
    ) {
        $this->_fieldCollection = $fieldCollection;
        $this->_request = $request;
    }

    /**
     * Get options array
     *
     * @return array
     */
    public function toOptionArray()
    {
        if (!$this->_options) {
            $fields = $this->getFieldsCollection();

            $options = [];
            $options[] = [
                'value' => '',
                'label' => __('-- Please select --')
            ];

            foreach ($fields as $field) {
                $type = $field->getType();
                $source = $field->getOptionsSource();
                if ($source && $source == 'country' && $type == 'select') {
                    $options[] = [
                        'value' => $field->getId(),
                        'label' => $field->getTitle()
                    ];
                }
            }
            $this->_options = $options;
        }

        return $this->_options;
    }

    /**
     * @return array|\Mageside\MultipleCustomForms\Model\ResourceModel\CustomForm\Field\Collection
     */
    public function getFieldsCollection()
    {
        $formId = $this->_request->getParam('form_id');
        if ($formId) {
            $this->_fieldCollection
                ->addFieldToFilter('form_id', ['eg' => (int) $formId]);

            return $this->_fieldCollection;
        }

        return [];
    }
}
