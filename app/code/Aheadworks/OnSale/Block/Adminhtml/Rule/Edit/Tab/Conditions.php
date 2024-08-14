<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Block\Adminhtml\Rule\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Magento\Framework\Data\FormFactory;
use Magento\Rule\Block\Conditions as ConditionsBlock;
use Magento\Backend\Block\Widget\Form\Renderer\FieldsetFactory;
use Magento\Framework\App\Request\DataPersistorInterface;
use Aheadworks\OnSale\Api\RuleRepositoryInterface;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\DataObject;
use Aheadworks\OnSale\Model\Rule\Product as ProductRule;
use Aheadworks\OnSale\Model\Rule\ProductFactory as ProductRuleFactory;
use Aheadworks\OnSale\Api\Data\RuleInterface;
use Aheadworks\OnSale\Ui\DataProvider\Rule\FormDataProvider as RuleFormDataProvider;

/**
 * Class Conditions
 *
 * @package Aheadworks\OnSale\Block\Adminhtml\Rule\Edit\Tab
 */
class Conditions extends Generic
{
    /**#@+
     * Constants defined for form with conditions
     */
    const FORM_NAME = 'aw_onsale_rule_form';
    const FORM_FIELDSET_NAME = 'product_conditions_fieldset';
    const CONDITION_FIELD_NAME = 'product_conditions';
    const FORM_ID_PREFIX = 'rule_';
    /**#@-*/

    /**
     * @var ProductRuleFactory
     */
    private $viewedProductRuleFactory;

    /**
     * @var RuleRepositoryInterface
     */
    protected $ruleRepository;

    /**
     * @var Conditions
     */
    protected $conditions;

    /**
     * @var FieldsetFactory
     */
    protected $rendererFieldsetFactory;

    /**
     * @var DataObjectProcessor
     */
    protected $dataObjectProcessor;

    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var DataObject
     */
    protected $dataObject;

    /**
     * @var array
     */
    protected $formData;

    /**
     * {@inheritdoc}
     */
    protected $_nameInLayout = 'rule_product_conditions';

    /**
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param ConditionsBlock $conditions
     * @param FieldsetFactory $rendererFieldsetFactory
     * @param DataPersistorInterface $dataPersistor
     * @param RuleRepositoryInterface $ruleRepository
     * @param DataObjectProcessor $dataObjectProcessor
     * @param DataObject $dataObject
     * @param ProductRuleFactory $viewedProductRuleFactory
     * @param array $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        ConditionsBlock $conditions,
        FieldsetFactory $rendererFieldsetFactory,
        DataPersistorInterface $dataPersistor,
        RuleRepositoryInterface $ruleRepository,
        DataObjectProcessor $dataObjectProcessor,
        DataObject $dataObject,
        ProductRuleFactory $viewedProductRuleFactory,
        array $data = []
    ) {
        $this->viewedProductRuleFactory = $viewedProductRuleFactory;
        $this->conditions = $conditions;
        $this->rendererFieldsetFactory = $rendererFieldsetFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->ruleRepository = $ruleRepository;
        $this->dataPersistor = $dataPersistor;
        $this->dataObject = $dataObject;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Retrieve key of condition data in the form data
     *
     * @return string
     */
    protected function getFormDataConditionKey()
    {
        return RuleInterface::PRODUCT_CONDITION;
    }

    /**
     * Retrieve fieldset name
     *
     * @return string
     */
    protected function getFieldsetName()
    {
        return self::FORM_FIELDSET_NAME;
    }

    /**
     * Retrieve name of condition rule class
     *
     * @return string
     */
    protected function getConditionRuleClassName()
    {
        return ProductRule::class;
    }

    /**
     * Retrieve condition field name
     *
     * @return string
     */
    protected function getConditionFieldName()
    {
        return self::CONDITION_FIELD_NAME;
    }

    /**
     * Retrieve form html id prefix
     *
     * @return string
     */
    protected function getFormHtmlIdPrefix()
    {
        return self::FORM_ID_PREFIX;
    }

    /**
     * Retrieve fieldset template
     *
     * @return string
     */
    protected function getFieldsetTemplate()
    {
        return 'Magento_CatalogRule::promo/fieldset.phtml';
    }

    /**
     * Retrieve url route of fieldset new child
     *
     * @return string
     */
    protected function getFieldsetNewChildUrlRoute()
    {
        return '*/*/newConditionHtml';
    }

    /**
     * Create form for controls
     *
     * @return \Magento\Framework\Data\Form
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function createForm()
    {
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix($this->getFormHtmlIdPrefix());
        return $form;
    }

    /**
     * Retrieve condition rule object from condition array
     *
     * @param mixed $conditionData
     * @return \Magento\Rule\Model\AbstractModel
     */
    protected function getConditionRule($conditionData)
    {
        $viewedProductRule = $this->viewedProductRuleFactory->create();
        if (isset($conditionData) && (is_array($conditionData))) {
            $viewedProductRule->setConditions([])
                ->getConditions()
                ->loadArray($conditionData);
        }
        return $viewedProductRule;
    }

    /**
     * {@inheritdoc}
     */
    protected function prepareFieldset($fieldset)
    {
        $conditionData = $this->getConditionData();
        $conditionRule = $this->getConditionRule($conditionData);
        $fieldset->setRenderer($this->getFieldsetRenderer());
        $conditionRule->setJsFormObject($this->getFormHtmlIdPrefix() . $this->getFieldsetName());
        $this->addFieldsToFieldset($fieldset, $conditionRule);
        $this->setConditionFormName(
            $conditionRule->getConditions(),
            self::FORM_NAME,
            $this->getFormHtmlIdPrefix() . $this->getFieldsetName()
        );
    }

    /**
     * Retrieve renderer for form fieldset
     *
     * @return \Magento\Framework\Data\Form\Element\Renderer\RendererInterface
     */
    protected function getFieldsetRenderer()
    {
        return $this->rendererFieldsetFactory->create()
            ->setTemplate($this->getFieldsetTemplate())
            ->setNewChildUrl(
                $this->getUrl(
                    $this->getFieldsetNewChildUrlRoute(),
                    [
                        'form'   => $this->getFormHtmlIdPrefix() . $this->getFieldsetName(),
                        'prefix' => $this->getConditionPrefix(),
                        'rule'   => base64_encode($this->getConditionRuleClassName()),
                        'form_namespace' => self::FORM_NAME
                    ]
                )
            );
    }

    /**
     * Add necessary fields to form fieldset
     *
     * @param \Magento\Framework\Data\Form\Element\Fieldset $fieldset
     * @param mixed $conditionData
     */
    protected function addFieldsToFieldset($fieldset, $conditionData)
    {
        $fieldset
            ->addField(
                $this->getConditionFieldName(),
                'text',
                [
                    'name' => $this->getConditionFieldName(),
                    'label' => __('Conditions'),
                    'title' => __('Conditions'),
                    'data-form-part' => self::FORM_NAME
                ]
            )
            ->setRule($conditionData)
            ->setRenderer($this->conditions);
    }

    /**
     * Handles addition of form name to condition and its conditions
     *
     * @param $conditions
     * @param string $formName
     * @param string $jsFormObject
     * @return void
     */
    protected function setConditionFormName($conditions, $formName, $jsFormObject)
    {
        $conditions->setFormName($formName);
        $conditions->setJsFormObject($jsFormObject);
        if ($conditions->getConditions() && is_array($conditions->getConditions())) {
            foreach ($conditions->getConditions() as $condition) {
                $this->setConditionFormName($condition, $formName, $jsFormObject);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareForm()
    {
        $form = $this->createForm();
        $fieldset = $this->addFieldsetToForm($form);
        $this->prepareFieldset($fieldset);
        $this->setForm($form);
        return parent::_prepareForm();
    }

    /**
     * Add fieldset to specified form
     *
     * @param \Magento\Framework\Data\Form $form
     * @return \Magento\Framework\Data\Form\Element\Fieldset
     */
    protected function addFieldsetToForm($form)
    {
        return $form->addFieldset(
            $this->getFieldsetName(),
            [
                'comment' => __(
                    'Please specify products where the rule should be applied'
                )
            ]
        );
    }

    /**
     * Retrieve condition data array from form data
     *
     * @return mixed|null
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function getConditionData()
    {
        $conditionData = null;
        $formData = $this->getFormData();
        if (is_array($formData) && (isset($formData[$this->getFormDataConditionKey()]))) {
            $conditionData = $formData[$this->getFormDataConditionKey()];
        }
        return $conditionData;
    }

    /**
     * Get data for rule blocks
     *
     * @return array|null
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function getFormData()
    {
        if ($this->formData === null) {
            $formData = [];
            if (!empty($this->dataPersistor->get(RuleFormDataProvider::DATA_PERSISTOR_FORM_DATA_KEY))) {
                $formData = $this->dataObject->setData(
                    $this->dataPersistor->get(RuleFormDataProvider::DATA_PERSISTOR_FORM_DATA_KEY)
                );
            }
            if (is_array($formData) && $id = $this->getRequest()->getParam('id')) {
                $formData = $this->ruleRepository->get($id);
            }
            if ($formData) {
                $this->formData = $this->dataObjectProcessor->buildOutputDataArray(
                    $formData,
                    RuleInterface::class
                );
            }
        }

        return $this->formData;
    }
}
