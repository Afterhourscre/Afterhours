<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Coupongenerator\Block\Adminhtml\Rule\Edit\Tab;

use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Rule\Model\Condition\AbstractCondition as RuleAbstractCondition;
use Aheadworks\Coupongenerator\Model\Converter\Condition as ConditionConverter;

/**
 * Class Actions
 * @package Aheadworks\Coupongenerator\Block\Adminhtml\Rule\Edit\Tab
 * @codeCoverageIgnore
 */
class Actions extends \Magento\Backend\Block\Widget\Form\Generic implements TabInterface
{
    /**
     * @var string
     */
    const FORM_NAME = 'aw_coupongenerator_rule_form';

    /**
     * @var string
     */
    protected $_nameInLayout = 'action_condition';

    /**
     * @var \Magento\Backend\Block\Widget\Form\Renderer\FieldsetFactory
     */
    private $rendererFieldsetFactory;

    /**
     * @var \Magento\Rule\Block\Actions
     */
    private $ruleActions;

    /**
     * @var \Magento\SalesRule\Model\RuleFactory
     */
    private $magentoSalesRuleFactory;

    /**
     * @var ConditionConverter
     */
    private $conditionConverter;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Backend\Block\Widget\Form\Renderer\FieldsetFactory $rendererFieldsetFactory
     * @param \Magento\Rule\Block\Actions $ruleActions
     * @param \Magento\SalesRule\Model\RuleFactory $magentoSalesRuleFactory
     * @param ConditionConverter $conditionConverter
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Backend\Block\Widget\Form\Renderer\FieldsetFactory $rendererFieldsetFactory,
        \Magento\Rule\Block\Actions $ruleActions,
        \Magento\SalesRule\Model\RuleFactory $magentoSalesRuleFactory,
        ConditionConverter $conditionConverter,
        array $data = []
    ) {
        $this->rendererFieldsetFactory = $rendererFieldsetFactory;
        $this->ruleActions = $ruleActions;
        $this->magentoSalesRuleFactory = $magentoSalesRuleFactory;
        $this->conditionConverter = $conditionConverter;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareForm()
    {
        /** @var \Magento\SalesRule\Api\Data\RuleInterface $salesRule */
        $salesRule = $this->_coreRegistry->registry('aw_coupongenerator_rule');

        /** @var \Magento\SalesRule\Model\Rule $magentoRuleModel */
        $magentoRuleModel = $this->magentoSalesRuleFactory->create();

        /** @var \Magento\SalesRule\Api\Data\ConditionInterface $actionCondition */
        $actionCondition = $salesRule->getActionCondition();
        if ($actionCondition) {
            $formActionCondition = $this->conditionConverter->dataModelToArray($actionCondition);
            $magentoRuleModel
                ->setActions([])
                ->getActions()
                ->loadArray($formActionCondition)
            ;
        }

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('rule_');

        $form = $this->addFieldsetToTab($form, '', $magentoRuleModel);

        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * {@inheritdoc}
     */
    public function getTabLabel()
    {
        return __('Actions');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return __('Actions');
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
        $fieldsetName = $prefix . 'actions_fieldset';
        $fieldset = $form
            ->addFieldset(
                $fieldsetName,
                [
                    'legend' => __(
                        'Apply the rule only to cart items matching the following conditions ' .
                        '(leave blank for all items).'
                    )
                ]
            )
            ->setRenderer(
                $this->rendererFieldsetFactory->create()
                    ->setTemplate('Magento_CatalogRule::promo/fieldset.phtml')
                    ->setNewChildUrl(
                        $this->getUrl(
                            '*/*/newActionHtml',
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
                $prefix . 'actions',
                'text',
                [
                    'name' => $prefix . 'actions',
                    'label' => __('Apply To'),
                    'title' => __('Apply To'),
                    'required' => true,
                    'data-form-part' => self::FORM_NAME
                ]
            )
            ->setRule($ruleModel)
            ->setRenderer($this->ruleActions);

        $this->setConditionFormName(
            $ruleModel->getActions(),
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
