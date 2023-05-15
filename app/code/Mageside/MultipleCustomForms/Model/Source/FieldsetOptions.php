<?php
/**
 * Copyright © Mageside. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */
namespace Mageside\MultipleCustomForms\Model\Source;

class FieldsetOptions implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private $request;

    /**
     * @var array
     */
    private $options;

    /**
     * @var \Mageside\MultipleCustomForms\Model\ResourceModel\CustomForm\Fieldset\CollectionFactory
     */
    private $fieldsetCollectionFactory;

    /**
     * FieldsetOptions constructor.
     * @param \Mageside\MultipleCustomForms\Model\ResourceModel\CustomForm\Fieldset\CollectionFactory $fieldsetCollectionFactory
     * @param \Magento\Framework\App\RequestInterface $request
     */
    public function __construct(
        \Mageside\MultipleCustomForms\Model\ResourceModel\CustomForm\Fieldset\CollectionFactory $fieldsetCollectionFactory,
        \Magento\Framework\App\RequestInterface $request
    ) {
        $this->fieldsetCollectionFactory = $fieldsetCollectionFactory;
        $this->request = $request;
    }

    /**
     * Get options array
     *
     * @return array
     */
    public function toOptionArray()
    {
        if (!$this->options) {
            $options = [];
            if ($formId = $this->request->getParam('form_id')) {
                /** @var \Mageside\MultipleCustomForms\Model\ResourceModel\CustomForm\Fieldset\Collection $fieldsetsCollection */
                $fieldsetsCollection = $this->fieldsetCollectionFactory->create();
                $fieldsetsCollection->addFieldToFilter('form_id', $formId);
                foreach ($fieldsetsCollection->getItems() as $fieldset) {
                    $options[] = [
                        'value' => $fieldset->getId(),
                        'label' => $fieldset->getName()
                    ];
                }
            }

            if (empty($options)) {
                $options[] = [
                    'value' => '',
                    'label' => __('-- Please add fieldset before continue --')
                ];
                $this->options = $options;

                return $this->options;
            }

            $this->options = array_merge(
                [
                    [
                        'value' => 0,
                        'label' => __('-- Please select --')
                    ]
                ],
                $options
            );
        }

        return $this->options;
    }
}
