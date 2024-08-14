<?php
/**
 * Copyright © Mageside. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */
namespace Mageside\MultipleCustomForms\Block\Widget;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Result\PageFactory;

class CustomForm extends AbstractBlock implements \Magento\Widget\Block\BlockInterface
{
    protected $_template = "widget/form.phtml";

    /**
     * @var \Mageside\MultipleCustomForms\Model\CustomForm
     */
    protected $_customForm;

    /**
     * @var \Mageside\MultipleCustomForms\Model\CustomFormFactory
     */
    protected $_customFormFactory;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $_resultPageFactory;

    /**
     * @var \Mageside\MultipleCustomForms\Helper\Config
     */
    protected $_configHelper;

    /**
     * CustomForm constructor.
     * @param Template\Context $context
     * @param \Mageside\MultipleCustomForms\Model\CustomFormFactory $customFormFactory
     * @param \Mageside\MultipleCustomForms\Helper\Config $configHelper
     * @param PageFactory $resultPageFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Mageside\MultipleCustomForms\Model\CustomFormFactory $customFormFactory,
        \Mageside\MultipleCustomForms\Helper\Config $configHelper,
        PageFactory $resultPageFactory,
        $data = []
    ) {
        $this->_customFormFactory = $customFormFactory;
        $this->_resultPageFactory = $resultPageFactory;
        $this->_configHelper = $configHelper;
        parent::__construct($context, $data);
    }

    /**
     * @return bool|\Mageside\MultipleCustomForms\Model\CustomForm
     */
    public function getForm()
    {
        if (!$formId = $this->getData('form_id')) {
            return false;
        }
        if (!$this->_customForm || $this->_customForm->getId() != $formId) {
            $this->_customForm = $this->_customFormFactory->create()->load($formId);
            if (!$this->_customForm->getId()) {
                return false;
            }
        }

        return $this->_customForm;
    }

    /**
     * @return array
     */
    public function getFormData()
    {
        return $this->getForm() ? $this->getForm()->toArray() : [];
    }

    /**
     * @return \Magento\Framework\Phrase|mixed
     */
    public function getButtonTitle()
    {
        return $this->getData('button_title')
            ? $this->getData('button_title')
            :  __('Show Form');
    }

    /**
     * @return string
     */
    public function getFormHtmlId()
    {
        return \Mageside\MultipleCustomForms\Model\CustomForm::FORM_PREFIX . $this->getForm()->getId();
    }

    /**
     * @return mixed
     */
    public function getFormCssClass()
    {
        return $this->getForm()->getCustomClass();
    }

    /**
     * @param $name
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getBlockHtmlByName($name)
    {
        $block = $this->getLayout()->getBlock($name);
        if ($block) {
            $block->setForm($this->getForm());
            return $block->toHtml();
        }

        return '';
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getFieldsHtml()
    {
        return $this->getBlockHtmlByName('custom.form.fields');
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getRecaptchaHtml()
    {
        return $this->getBlockHtmlByName('custom.form.recaptcha');
    }

    /**
     * @return string
     */
    public function getSubmitUrl()
    {
        return $this->getUrl('customform/form/post');
    }

    /**
     * @return bool
     */
    public function canShowForm()
    {
        if (!$this->_configHelper->isEnabled()) {
            return false;
        }

        $form = $this->getForm();

        if ($form && $form->getId() && $form->getFormStatus() == 1) {
            return true;
        }

        return false;
    }

    /**
     * @return string
     */
    public function getJsonFormConfig()
    {
        $form = $this->getForm();
        $config = [
            'formId'            => $form->getId(),
            'afterSubmit'       => $form->getAfterSubmit(),
            'redirectUrl'       => $form->getRedirectUrl(),
            'successMessage'    => $form->getSuccessMessage(),
            'display'           => $this->getData('form_display'),
            'modalSettings'     => [
                'title'             => $form->getName(),
                'formCode'          => $form->getCode(),
                'buttonSelector'    => '#custom-form-open-modal-button-' . $form->getId(),
            ],
            'reCaptcha'             => [
                'show' => $form->getRecaptcha(),
                'inputSelector' => '#g-recaptcha-response-' . $form->getId()
            ]
        ];

        return $this->jsonEncode($config);
    }
}
