<?php
/**
 * @category    WeltPixel
 * @package     WeltPixel_EnhancedEmail
 */

namespace WeltPixel\EnhancedEmail\Plugin;

/**
 * Class EmailTemplateFormPlugin
 * @package WeltPixel\EnhancedEmail\Plugin
 */
class EmailTemplateFormPlugin
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * EmailTemplateFormPlugin constructor.
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        \Magento\Framework\Registry $registry
    )
    {
        $this->_coreRegistry = $registry;
    }

    /**
     * @param \Magento\Email\Block\Adminhtml\Template\Edit\Form $subject
     * @param \Closure $proceed
     * @return mixed
     */
    public function aroundGetFormHtml(
        \Magento\Email\Block\Adminhtml\Template\Edit\Form $subject,
        \Closure $proceed
    )
    {
        $emailTemplate = $this->_coreRegistry->registry('current_email_template');

        if (!$emailTemplate) {
            // Handle the case where the email template is not set
            return $proceed();
        }

        $form = $subject->getForm();
        if (is_object($form)) {
            $fieldset = $form->getElement('base_fieldset');
            $fieldset->addField(
                'template_preheader',
                'textarea',
                [
                    'name' => 'template_preheader',
                    'label' => __('Email First Line'),
                    'id' => 'template_preheader',
                    'required' => false,
                    'onkeyup' => 'templateControl.updateTemplateContent(this);',
                    'note' => 'Email preheader content.',
                    'value' => $emailTemplate->getTemplatePreheader()
                ],
                'template_subject'
            );

            $subject->setForm($form);
        }

        return $proceed();
    }
}
