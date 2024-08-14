<?php
/**
 * Copyright © Mageside. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */
namespace Mageside\MultipleCustomForms\Block\Widget\CustomForm;

class Recaptcha extends \Mageside\MultipleCustomForms\Block\Widget\AbstractBlock
{
    /**
     * @var \Mageside\MultipleCustomForms\Model\CustomForm
     */
    private $_form;

    /**
     * @var \Magento\Framework\Registry|null
     */
    private $_coreRegistry = null;

    /**
     * @var \Magento\Framework\Math\Random
     */
    private $_mathRandom;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\Math\Random $mathRandom
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\Math\Random $mathRandom,
        array $data = []
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->_mathRandom = $mathRandom;
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
     * @return bool|\Mageside\MultipleCustomForms\Model\CustomForm
     */
    public function getForm()
    {
        return $this->_form;
    }

    /**
     * @return bool
     */
    public function canShowReCaptcha()
    {
        $form = $this->getForm();
        if ($form->isReCaptchaEnabled()) {
            return true;
        }

        return false;
    }

    /**
     * @return bool
     */
    public function isNeedToLoadReCaptchaScript()
    {
        if (!$this->_coreRegistry->registry('isReCaptchaInitialized')) {
            $this->_coreRegistry->register('isReCaptchaInitialized', true);
            return true;
        }

        return false;
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getWidgetId()
    {
        return $this->_mathRandom->getRandomString(6);
    }
}
