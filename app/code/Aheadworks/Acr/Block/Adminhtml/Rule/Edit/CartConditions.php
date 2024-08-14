<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Acr\Block\Adminhtml\Rule\Edit;

use Aheadworks\Acr\Api\Data\RuleInterface;
use Aheadworks\Acr\Model\Rule\Cart as CartRule;
use Aheadworks\Acr\Model\Rule\Converter as RuleConverter;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Backend\Block\Widget\Form\Renderer\FieldsetFactory as RendererFieldsetFactory;
use Magento\Rule\Model\Condition\AbstractCondition as RuleAbstractCondition;
use Magento\Rule\Block\Conditions as BlockConditions;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;

/**
 * Class CartConditions
 * @package Aheadworks\Acr\Block\Adminhtml\Rule\Edit
 * @codeCoverageIgnore
 */
class CartConditions extends \Magento\Backend\Block\Widget\Form\Generic implements TabInterface
{
    /**
     * @var string
     */
    const FORM_NAME = 'aw_acr_rule_form';

    /**
     * @var string
     */
    protected $_nameInLayout = 'cart_conditions';

    /**
     * @var RendererFieldsetFactory
     */
    private $rendererFieldsetFactory;

    /**
     * @var BlockConditions
     */
    private $conditions;

    /**
     * @var RuleConverter
     */
    private $ruleConverter;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param BlockConditions $conditions
     * @param RendererFieldsetFactory $rendererFieldsetFactory
     * @param RuleConverter $ruleConverter
     * @param array $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        BlockConditions $conditions,
        RendererFieldsetFactory $rendererFieldsetFactory,
        RuleConverter $ruleConverter,
        array $data = []
    ) {
        $this->rendererFieldsetFactory = $rendererFieldsetFactory;
        $this->conditions = $conditions;
        $this->ruleConverter = $ruleConverter;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareForm()
    {
        /** @var RuleInterface $rule */
        $rule = $this->_coreRegistry->registry('aw_acr_rule');

        /** @var CartRule $cartRuleModel */
        $cartRuleModel = $this->ruleConverter->getCartRule($rule);

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('rule_');

        $form = $this->addFieldsetToTab($form, 'cart_', $cartRuleModel);

        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * {@inheritdoc}
     */
    public function getTabLabel()
    {
        return __('Conditions');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return __('Conditions');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Add fieldset to form
     *
     * @param \Magento\Framework\Data\Form $form
     * @param string $prefix
     * @param mixed $ruleModel
     * @return \Magento\Framework\Data\Form
     */
    private function addFieldsetToTab($form, $prefix, $ruleModel)
    {
        $fieldsetName = $prefix . 'conditions_fieldset';
        $fieldset = $form
            ->addFieldset(
                $fieldsetName,
                [
                    'legend' => __('Cart Conditions')
                ]
            )
            ->setRenderer(
                $this->rendererFieldsetFactory->create()
                    ->setTemplate('Magento_CatalogRule::promo/fieldset.phtml')
                    ->setNewChildUrl(
                        $this->getUrl(
                            'sales_rule/promo_quote/newConditionHtml',
                            [
                                'form'   => $form->getHtmlIdPrefix() . $fieldsetName,
                                'form_namespace' => self::FORM_NAME
                            ]
                        )
                    )
            )
        ;

        $ruleModel->setJsFormObject($form->getHtmlIdPrefix() . $fieldsetName);

        $fieldset
            ->addField(
                $prefix . 'conditions',
                'text',
                [
                    'name' => $prefix . 'conditions',
                    'label' => __('Conditions'),
                    'title' => __('Conditions'),
                    'data-form-part' => self::FORM_NAME
                ]
            )
            ->setRule($ruleModel)
            ->setRenderer($this->conditions);

        $this->setConditionFormName(
            $ruleModel->getConditions(),
            self::FORM_NAME,
            $form->getHtmlIdPrefix() . $fieldsetName
        );

        return $form;
    }

    /**
     * Handles addition of form name to condition and its conditions
     *
     * @param RuleAbstractCondition $conditions
     * @param string $formName
     * @param string $jsFormObject
     * @return void
     */
    protected function setConditionFormName(RuleAbstractCondition $conditions, $formName, $jsFormObject)
    {
        $conditions->setFormName($formName);
        $conditions->setJsFormObject($jsFormObject);
        if ($conditions->getConditions() && is_array($conditions->getConditions())) {
            foreach ($conditions->getConditions() as $condition) {
                $this->setConditionFormName($condition, $formName, $jsFormObject);
            }
        }
    }
}
