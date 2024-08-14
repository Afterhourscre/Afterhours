<?php
/**
 * Copyright © Mageside. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */
namespace Mageside\MultipleCustomForms\Block\Widget\CustomForm\Fields\Type;

class ProductAttributeType extends \Mageside\MultipleCustomForms\Block\Widget\CustomForm\Fields\Type\SelectType
{
    /**
     * @var \Magento\Catalog\Model\Product\Attribute\Repository
     */
    protected $_attributeRepository;

    /**
     * ProductAttributeType constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Catalog\Model\Product\Attribute\Repository $attributeRepository
     * @param \Mageside\MultipleCustomForms\Model\CustomForm\Field\Settings $fieldSettings
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Catalog\Model\Product\Attribute\Repository $attributeRepository,
        \Mageside\MultipleCustomForms\Model\CustomForm\Field\Settings $fieldSettings,
        array $data = []
    ) {
        $this->_attributeRepository = $attributeRepository;
        parent::__construct($context, $fieldSettings, $data);
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        $options = [];
        $attributeOptions = $this->_attributeRepository
            ->get($this->getField()->getProductAttribute())
            ->getOptions();
        foreach ($attributeOptions as $attributeOption) {
            $options[] =
                [
                    'value' => $attributeOption->getValue(),
                    'label' => $attributeOption->getLabel()
                ];
        }

        return $options;
    }
}
