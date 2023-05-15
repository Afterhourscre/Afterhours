<?php
/**
 * Copyright © Mageside. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */
namespace Mageside\MultipleCustomForms\Model\Source\Widget;

class FormList implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var array
     */
    protected $_options;

    /**
     * @var \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
     */
    protected $_customFormsCollection;

    /**
     * FormList constructor.
     * @param \Mageside\MultipleCustomForms\Model\ResourceModel\CustomForm\Collection $customFormsCollection
     */
    public function __construct(
        \Mageside\MultipleCustomForms\Model\ResourceModel\CustomForm\Collection $customFormsCollection
    ) {
        $this->_customFormsCollection = $customFormsCollection;
    }

    public function toOptionArray()
    {
        if (!$this->_options) {
            $items = $this->_customFormsCollection->getItems();

            $options = [];
            $options[] = [
                'value' => '',
                'label' => __('-- Please Select --')
            ];
            foreach ($items as $item) {
                $options[] = [
                    'value' => $item->getId(),
                    'label' => $item->getName()
                ];
            }
            $this->_options = $options;
        }

        return $this->_options;
    }
}
