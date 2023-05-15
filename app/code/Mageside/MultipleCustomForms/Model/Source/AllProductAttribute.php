<?php
/**
 * Copyright © Mageside. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */
namespace Mageside\MultipleCustomForms\Model\Source;

class AllProductAttribute implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var \Mageside\MultipleCustomForms\Model\ResourceModel\AttributeResource
     */
    protected $_attributeResource;

    /**
     * @var array
     */
    protected $_options;

    /**
     * AllProductAttribute constructor.
     * @param \Mageside\MultipleCustomForms\Model\ResourceModel\AttributeResource $attributeResource
     */
    public function __construct(
        \Mageside\MultipleCustomForms\Model\ResourceModel\AttributeResource $attributeResource
    ) {
        $this->_attributeResource = $attributeResource;
    }

    /**
     * Get options array
     *
     * @return array
     */
    public function toOptionArray()
    {
        if (!$this->_options) {
            $attributes = $this->_attributeResource->getProductAttributes();

            $options = [];
            $options[] = [
                'value' => '',
                'label' => __('-- Please select --')
            ];
            $options[] = [
                'value' => 'entity_id',
                'label' => __('Product Id')
            ];

            foreach ($attributes as $attribute) {
                $options[] = [
                    'value' => $attribute['attribute_code'],
                    'label' => $attribute['frontend_label']
                ];
            }
            $this->_options = $options;
        }

        return $this->_options;
    }
}
