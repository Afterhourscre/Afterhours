<?php
/**
 * Copyright © Mageside. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */
namespace Mageside\MultipleCustomForms\Model\Source;

class FieldOptions implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private $_request;

    /**
     * @var array
     */
    private $_options;

    /**
     * @var \Mageside\MultipleCustomForms\Model\CustomForm\FieldFactory
     */
    private $_fieldFactory;

    /**
     * FieldOptions constructor.
     * @param \Mageside\MultipleCustomForms\Model\CustomForm\FieldFactory $fieldFactory
     * @param \Magento\Framework\App\RequestInterface $request
     */
    public function __construct(
        \Mageside\MultipleCustomForms\Model\CustomForm\FieldFactory $fieldFactory,
        \Magento\Framework\App\RequestInterface $request
    ) {
        $this->_fieldFactory = $fieldFactory;
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
            $options = [];
            $fieldId = $this->_request->getParam('record_id');
            if ($fieldId) {
                $field = $this->_fieldFactory->create()->load($fieldId);
                if ($field->getId()) {
                    $options = $field->getOptions();
                }
            }

            if (empty($options)) {
                $options[] = [
                    'value' => '',
                    'label' => __('-- Please add options before continue --')
                ];
                $this->_options = $options;

                return $this->_options;
            }

            $this->_options = $options;
        }

        return $this->_options;
    }
}
