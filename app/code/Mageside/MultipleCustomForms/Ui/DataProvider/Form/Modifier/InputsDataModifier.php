<?php
/**
 * Copyright © Mageside. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */
namespace Mageside\MultipleCustomForms\Ui\DataProvider\Form\Modifier;

use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;

class InputsDataModifier implements ModifierInterface
{
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private $request;

    /**
     * @var \Mageside\MultipleCustomForms\Model\CustomFormFactory
     */
    private $formFactory;

    /**
     * InputsDataModifier constructor.
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Mageside\MultipleCustomForms\Model\CustomFormFactory $formFactory
     */
    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \Mageside\MultipleCustomForms\Model\CustomFormFactory $formFactory
    ) {
        $this->request = $request;
        $this->formFactory = $formFactory;
    }

    public function modifyMeta(array $meta)
    {
        return $meta;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyData(array $data)
    {
        if ($form_id = $this->request->getParam('id')) {
            /** @var \Mageside\MultipleCustomForms\Model\CustomForm $form */
            $form = $this->formFactory->create()->load($form_id);
            if ($form->getId()) {
                $inputsCollection = $form->getFieldCollection()->addOrderByPosition();
                $fieldsets = $form->getFieldsetCollection()->addOrderByPosition()->getItems();
                $inputsData = [];
                if (!empty($inputsCollection)) {
                    foreach ($inputsCollection->getItems() as $input) {
                        $fieldsetName = '';
                        if (isset($fieldsets[$input->getData('fieldset_id')])) {
                            $fieldsetName = $fieldsets[$input->getData('fieldset_id')]->getData('name');
                        }
                        $inputsData[] = [
                            'id'            => $input->getData('id'),
                            'title'         => $input->getData('title'),
                            'type'          => $input->getData('type'),
                            'fieldset'      => $fieldsetName,
                            'required'      => $input->getData('required') ? __('yes') : '',
                            'position'      => $input->getData('position'),
                        ];
                    }
                }

                $data[$this->request->getParam('id')]['form']['inputs'] = $inputsData;
            }
        }

        return $data;
    }
}
