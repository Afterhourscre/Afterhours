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
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Config\Model\Config\Source\Yesno;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;
use Mageplaza\CallForPrice\Model\Action;
use Mageplaza\CallForPrice\Model\QuoteFields;
use Mageplaza\CallForPrice\Model\RequireFields;

/**
 * Class Whattoshow
 * @package Mageplaza\CallForPrice\Block\Adminhtml\Rules\Edit\Tab
 */
class Whattoshow extends Generic implements TabInterface
{
    /**
     * @var Yesno
     */
    protected $_yesno;

    /**
     * @var Action
     */
    protected $_action;

    /**
     * @var QuoteFields
     */
    protected $_quotefields;

    /**
     * @var RequireFields
     */
    protected $_requirefields;

    /**
     * Whattoshow constructor.
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param Yesno $yesno
     * @param Action $action
     * @param QuoteFields $quotefields
     * @param RequireFields $requirefields
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Yesno $yesno,
        Action $action,
        QuoteFields $quotefields,
        RequireFields $requirefields,
        array $data = []
    )
    {
        $this->_yesno         = $yesno;
        $this->_action        = $action;
        $this->_quotefields   = $quotefields;
        $this->_requirefields = $requirefields;

        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * @return Generic
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareForm()
    {
        /** @var \Mageplaza\CallForPrice\Model\Rules $rule */
        $rule = $this->_coreRegistry->registry('current_rule');
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('rule_');
        $form->setFieldNameSuffix('rule');
        $htmlIdPrefix = $form->getHtmlIdPrefix();

        $fieldset = $form->addFieldset('base_fieldset', [
            'legend' => __('What To Show'),
            'class'  => 'fieldset-wide'
        ]);

        $iditemEditing = $this->getRequest()->getParam('rule_id');
        if (!$iditemEditing) {
            $fieldset->addField('button_label', 'text', [
                'name'     => 'button_label',
                'label'    => __('Button Label'),
                'title'    => __('Button Label'),
                'value'    => __("Call For Price"),
                'required' => true,
            ]);
        } else {
            $fieldset->addField('button_label', 'text', [
                'name'     => 'button_label',
                'label'    => __('Button Label'),
                'title'    => __('Button Label'),
                'required' => true,
            ]);
        }

        $fieldset->addField('action', 'select', [
            'name'     => 'action',
            'label'    => __('Action'),
            'title'    => __('Action'),
            'required' => true,
            'values'   => $this->_action->toOptionArray()
        ]);

        $fieldset->addField('url_redirect', 'text', [
            'name'     => 'url_redirect',
            'label'    => __('Redirect URL'),
            'title'    => __('Redirect URL'),
            'note'     => __('Enter a URL here, visitor will be redirected to this URL when the button is clicked'),
            'required' => true,
        ]);

        if (!$iditemEditing) {
            $fieldset->addField('quote_heading', 'text', [
                'name'     => 'quote_heading',
                'label'    => __('Quote Heading'),
                'title'    => __('Quote Heading'),
                'value'    => __("Get a quote"),
                'required' => false,
            ]);

            $fieldset->addField('quote_description', 'textarea', [
                'name'     => 'quote_description',
                'label'    => __('Quote Description'),
                'title'    => __('Quote Description'),
                'value'    => __("Jot us a note and we’ll get back to you as quickly as possible."),
                'required' => false,
            ]);
        } else {
            $fieldset->addField('quote_heading', 'text', [
                'name'     => 'quote_heading',
                'label'    => __('Quote Heading'),
                'title'    => __('Quote Heading'),
                'required' => false,
            ]);

            $fieldset->addField('quote_description', 'textarea', [
                'name'     => 'quote_description',
                'label'    => __('Quote Description'),
                'title'    => __('Quote Description'),
                'required' => false,
            ]);
        }

        $fieldset->addField('show_fields', 'multiselect', [
            'name'   => 'show_fields',
            'label'  => __('Show Fields'),
            'title'  => __('Show Fields'),
            'note'   => __('If no fields are chosen, Email and Note are shown by default'),
            'values' => $this->_quotefields->toOptionArray()
        ]);

        $fieldset->addField('required_fields', 'multiselect', [
            'name'   => 'required_fields',
            'label'  => __('Required Fields'),
            'title'  => __('Required Fields'),
            'values' => $this->_requirefields->toOptionArray()
        ]);

        $fieldset->addField('enable_terms', 'select', [
            'name'   => 'enable_terms',
            'label'  => __('Enable Terms and Condtions'),
            'title'  => __('Enable Terms and Condtions'),
            'values' => $this->_yesno->toOptionArray()
        ]);

        $Lastfield = $form->getElement('enable_terms');
        $Lastfield->setAfterElementHtml(
            '<div data-mage-init=\'{
                    "Mageplaza_CallForPrice/js/actionfield": {}
                }\'>
                </div>'
        );

        $this->setChild('form_after',
            $this->getLayout()->createBlock('Magento\Backend\Block\Widget\Form\Element\Dependence')
                ->addFieldMap("{$htmlIdPrefix}action", 'action')
                ->addFieldMap("{$htmlIdPrefix}url_redirect", 'url_redirect')
                ->addFieldDependence('url_redirect', 'action', 'redirect_url')
                ->addFieldMap("{$htmlIdPrefix}quote_heading", 'quote_heading')
                ->addFieldDependence('quote_heading', 'action', 'popup_quote_form')
                ->addFieldMap("{$htmlIdPrefix}quote_description", 'quote_description')
                ->addFieldDependence('quote_description', 'action', 'popup_quote_form')
                ->addFieldMap("{$htmlIdPrefix}show_fields", 'show_fields')
                ->addFieldDependence('show_fields', 'action', 'popup_quote_form')
                ->addFieldMap("{$htmlIdPrefix}required_fields", 'required_fields')
                ->addFieldDependence('required_fields', 'action', 'popup_quote_form')
        );

        $form->addValues($rule->getData());
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
        return __('What To Show');
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
