<?php
/**
 * Copyright © Mageside. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */
namespace Mageside\MultipleCustomForms\Block\Widget\CustomForm;

class Fields extends \Mageside\MultipleCustomForms\Block\Widget\AbstractBlock
{
    /**
     * @var \Mageside\MultipleCustomForms\Model\CustomForm
     */
    private $_form;

    /**
     * @var null|array|\Mageside\MultipleCustomForms\Model\ResourceModel\CustomForm\Field\Collection
     */
    private $_fields = null;

    /**
     * @var null|\Mageside\MultipleCustomForms\Model\ResourceModel\CustomForm\Fieldset\Collection
     */
    private $_fieldsets = null;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    /**
     * @param $form
     * @return $this
     */
    public function setForm($form)
    {
        $this->_form = $form;

        return $this;
    }

    /**
     * Get fieldsets collection
     *
     * @return \Mageside\MultipleCustomForms\Model\ResourceModel\CustomForm\Fieldset\Collection|null
     * @throws \Exception
     */
    public function getFieldsets()
    {
        $fieldsetCollection = $this->_form->getFieldsetCollection()->addOrderByPosition();
        $fieldsets = $fieldsetCollection->getItems();
        $fields = $this->getFields()->getItems();

        $fieldsByFieldsets = [];
        if (!empty($fields)) {
            foreach ($fields as $field) {
                $fieldsByFieldsets[$field->getData('fieldset_id')][$field->getData('id')] = $field;
            }
        }

        if (!empty($fieldsets)) {
            foreach ($fieldsets as $fieldset) {
                if (!empty($fieldsByFieldsets[$fieldset->getData('id')])) {
                    $fieldset->setData('fields', $fieldsByFieldsets[$fieldset->getData('id')]);
                }
            }
        }

        if (!empty($fieldsByFieldsets[0])) {
            $emptyFieldset = $fieldsetCollection->getNewEmptyItem();
            $emptyFieldset->setData('fields', $fieldsByFieldsets[0]);
            $emptyFieldset->setData('name', 'no-title');
            $emptyFieldset->setData('id', 0);
            $fieldsetCollection->addItem($emptyFieldset);
        }

        return $fieldsetCollection;
    }

    /**
     * Get fields collection
     *
     * @return array|\Mageside\MultipleCustomForms\Model\ResourceModel\CustomForm\Field\Collection|null
     */
    public function getFields()
    {
        return $this->_form->getFieldCollection();
    }

    /**
     * Get fields html
     *
     * @param \Mageside\MultipleCustomForms\Model\CustomForm\Field $field
     * @return mixed
     */
    public function getFieldHtml(\Mageside\MultipleCustomForms\Model\CustomForm\Field $field)
    {
        $type = $field->getType();
        $renderer = $this->getChildBlock($type . '_' . $field->getOptionsSource());
        $type = $renderer ? $type . '_' . $field->getOptionsSource() : $type;

        if (!$renderer) {
            $renderer = $this->getChildBlock($type);
        }

        if ($renderer) {
            $renderer->setField($field);

            return $this->getChildHtml($type, false);
        }

        return '';
    }
}
