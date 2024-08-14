<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_CallForPrice
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\CallForPrice\Block\Adminhtml\Rules\Edit\Tab;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Form\Renderer\Fieldset;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;
use Magento\Rule\Block\Conditions as MagentoCondition;

/**
 * Class Wheretoshow
 * @package Mageplaza\CallForPrice\Block\Adminhtml\Rule\Edit\Tab
 */
class Wheretoshow extends Generic implements TabInterface
{
    /**
     * @var MagentoCondition
     */
    protected $_conditions;

    /**
     * @var \Magento\Backend\Block\Widget\Form\Renderer\Fieldset
     */
    protected $_rendererFieldset;

    /**
     * Conditions constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param MagentoCondition $conditions
     * @param Fieldset $rendererFieldset
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        MagentoCondition $conditions,
        Fieldset $rendererFieldset,
        array $data = []
    )
    {
        $this->_conditions       = $conditions;
        $this->_rendererFieldset = $rendererFieldset;

        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * @return Generic
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareForm()
    {
        /** @var \Mageplaza\CallForPrice\Model\Rule $rule */
        $rule   = $this->_coreRegistry->registry('current_rule');
        $ruleId = $this->getRequest()->getParam('rule_id');
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('rule_');
        $form->setFieldNameSuffix('rule');

        $renderer = $this->_rendererFieldset->setTemplate(
            'Mageplaza_CallForPrice::rule/fieldset.phtml'
        )->setNewChildUrl(
            $this->getUrl('mpcallforprice/condition/newConditionHtml/form/rule_conditions_fieldset')
        );

        if ($rule->getRuleId()) {
            $renderer->setAjaxUrl($this->getUrl('mpcallforprice/grid/productlist', ['id' => $ruleId, 'form_key' => $this->formKey->getFormKey(), 'loadGrid' => 1]));
        }

        $fieldset = $form->addFieldset('conditions_fieldset', [
            'legend' => __('Apply the rule only if the following conditions are met (leave blank for all products).'),
        ])->setRenderer($renderer);

        $fieldset->addField(
            'conditions',
            'text',
            ['name' => 'conditions', 'label' => __('Conditions'), 'title' => __('Conditions')]
        )->setRule($rule)->setRenderer($this->_conditions);

        $form->addValues($rule->getData());
        $rule->getConditions()->setJsFormObject('rule_conditions_fieldset');
        $this->setConditionFormName($rule->getConditions(), 'rule_conditions_fieldset');
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Where To Show');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->getTabLabel();
    }

    /**
     * Can show tab in tabs
     *
     * @return boolean
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Tab is hidden
     *
     * @return boolean
     */
    public function isHidden()
    {
        return false;
    }
}
