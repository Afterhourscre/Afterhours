<?php
/**
 * Copyright © Mageside. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */
namespace Mageside\MultipleCustomForms\Ui\DataProvider\Form\Modifier;

use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;

class FieldsetsDataModifier implements ModifierInterface
{
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private $request;

    /**
     * @var \Mageside\MultipleCustomForms\Model\CustomForm\FieldsetFactory
     */
    private $collection;

    /**
     * FieldsetsDataModifier constructor.
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Mageside\MultipleCustomForms\Model\ResourceModel\CustomForm\Fieldset\Collection $fieldCollection
     */
    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \Mageside\MultipleCustomForms\Model\ResourceModel\CustomForm\Fieldset\Collection $fieldCollection
    ) {
        $this->request = $request;
        $this->collection = $fieldCollection;
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
        $form_id = $this->request->getParam('id');
        $fieldsetsCollection = $this->collection
            ->addFieldToFilter('form_id', ['eq' => $form_id])
            ->addOrder('position', \Magento\Framework\Data\Collection::SORT_ORDER_ASC);

        $fieldsetsData = [];
        if (!empty($fieldsetsCollection)) {
            foreach ($fieldsetsCollection->getItems() as $fieldset) {
                $fieldsetsData[] = [
                    'id'        => $fieldset->getData('id'),
                    'name'      => $fieldset->getData('name'),
                    'title'     => $fieldset->getData('title') ? $fieldset->getData('title') : '',
                    'position'  => $fieldset->getData('position'),
                ];
            }
        }

        $data[$this->request->getParam('id')]['form']['fieldsets'] = $fieldsetsData;

        return $data;
    }
}
