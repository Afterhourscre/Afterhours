<?php
/**
 * @category    WeltPixel
 * @package     WeltPixel_EnhancedEmail
 * @copyright   Copyright (c) 2018 Weltpixel
 * @author      Weltpixel TEAM
 */

namespace WeltPixel\EnhancedEmail\Plugin;

/**
 * Class Transportbuilder
 * @package WeltPixel\EnhancedEmail\Plugin
 */
class Transportbuilder
{
    /**
     * @var \Magento\Framework\Registry|null
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Email\Model\BackendTemplate
     */
    protected $_backendTemplate;

    /**
     * Transportbuilder constructor.
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Email\Model\BackendTemplate $backendTemplate
     */
    public function __construct(
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Email\Model\BackendTemplate $backendTemplate
    )
    {
        $this->_coreRegistry = $coreRegistry;
        $this->_backendTemplate = $backendTemplate;
    }

    /**
     * @param \Magento\Framework\Mail\Template\TransportBuilder $subject
     * @param $templateIdentifier
     */
    public function beforeSetTemplateIdentifier(\Magento\Framework\Mail\Template\TransportBuilder $subject, $templateIdentifier)
    {
        $this->_initTemplate($templateIdentifier);
    }

    /**
     * Load email template
     *
     * @param string $idFieldName
     * @return \Magento\Email\Model\BackendTemplate $model
     */
    protected function _initTemplate($templateIdentifier)
    {
        if ($templateIdentifier) {
            $model = $this->_backendTemplate->load($templateIdentifier);
        }
        if (!$this->_coreRegistry->registry('email_template')) {
            $this->_coreRegistry->register('email_template', $model);
        }

        return $model;
    }
}
