<?php
/**
 * Copyright © Mageside. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */
namespace Mageside\MultipleCustomForms\Ui\DataProvider\Form;

use Magento\Ui\DataProvider\Modifier\ModifierInterface;
use Magento\Ui\DataProvider\Modifier\PoolInterface;
use Mageside\MultipleCustomForms\Model\CustomForm\Field\Settings;

class FormFieldsetDataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * @var null|\Mageside\MultipleCustomForms\Model\CustomForm\Fieldset
     */
    private $fieldset = null;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private $request;

    /**
     * @var \Mageside\MultipleCustomForms\Model\CustomForm\FieldsetFactory
     */
    private $fieldsetFactory;

    /**
     * @var \Mageside\MultipleCustomForms\Helper\Config
     */
    private $configHelper;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * FormFieldsetDataProvider constructor.
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Mageside\MultipleCustomForms\Model\CustomForm\FieldsetFactory $fieldsetFactory
     * @param \Mageside\MultipleCustomForms\Helper\Config $configHelper
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        \Magento\Framework\App\RequestInterface $request,
        \Mageside\MultipleCustomForms\Model\CustomForm\FieldsetFactory $fieldsetFactory,
        \Mageside\MultipleCustomForms\Helper\Config $configHelper,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);

        $this->request = $request;
        $this->fieldsetFactory = $fieldsetFactory;
        $this->configHelper = $configHelper;
        $this->storeManager = $storeManager;

        $storeId = (int) $this->request->getParam('store', 0);
        $store = $this->storeManager->getStore($storeId);
        $this->storeManager->setCurrentStore($store->getCode());
    }

    public function getMeta()
    {
        $fieldset = $this->getFieldset();

        if (!empty($fieldset->getData('useDefault')) && $this->request->getParam('store')) {
            foreach ($fieldset->getData('useDefault') as $title => $usedDefault) {
                $this->titleUsedDefault($title, $usedDefault);
            }
        }

        return $this->meta;
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        $formId = $this->request->getParam('form_id');
        if ($formId !== null) {
            $this->data['config']['data']['fieldset']['form_id'] = (int)$formId;
        }

        $fieldset = $this->getFieldset();
        if ($fieldset->getId()) {
            $this->data['config']['data']['fieldset'] = $fieldset->toArray();
        }

        $this->data['config']['data']['fieldset']['store_id'] = (int) $this->storeManager->getStore()->getId();

        return $this->data;
    }

    /**
     * @param $titleIndex
     * @param $usedDefault
     * @return $this
     */
    protected function titleUsedDefault($titleIndex, $usedDefault)
    {
        $useDefaultConfig = [
            'usedDefault'   => $usedDefault,
            'disabled'      => $usedDefault,
            'service'       => [
                'template'  => 'ui/form/element/helper/service',
            ]
        ];
        $this->meta['fieldset']['children'][$titleIndex]['arguments']['data']['config'] = $useDefaultConfig;

        return $this;
    }

    protected function getFieldset()
    {
        if ($this->fieldset === null) {
            /** @var \Mageside\MultipleCustomForms\Model\CustomForm\Fieldset $fieldset */
            $fieldset = $this->fieldsetFactory->create();
            if ($fieldsetId = $this->request->getParam('record_id')) {
                $fieldset->load((int)$fieldsetId, 'id');
            }
            $this->fieldset = $fieldset;
        }

        return $this->fieldset;
    }

    /**
     * @inheritdoc
     */
    public function addFilter(\Magento\Framework\Api\Filter $filter)
    {
    }
}
