<?php
/**
 * Copyright © Mageside. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */
namespace Mageside\MultipleCustomForms\Block\Adminhtml\Edit;

/**
 * Class Preview
 * @package Mageside\MultipleCustomForms\Block\Adminhtml\Edit
 */
class Preview extends GenericButton
{
    /**
     * @var \Mageside\MultipleCustomForms\Model\Source\Widget\FormTemplate
     */
    protected $_formTemplateList;

    /**
     * Constructor
     *
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        \Mageside\MultipleCustomForms\Model\Source\Widget\FormTemplate $formTemplateList
    ) {
        $this->_formTemplateList = $formTemplateList;
        parent::__construct($context, $registry);
    }

    /**
     * @return array
     */
    public function getButtonData()
    {
        if ($formId = $this->getRequest()->getParam('id')) {
            return  [
                'id'            => 'action_list',
                'label'         => __('Preview'),
                'class_name'    => 'Mageside\MultipleCustomForms\Block\Adminhtml\Edit\SecondarySplit',
                'onclick'       => "window.open('"
                    . $this->getPreviewUrl($formId, $this->_formTemplateList->toOptionArray()[0]['value'])
                    . "')",
                'sort_order'    => 20,
                'options'       => $this->_getActionOptions($formId),
            ];
        }

        return [];
    }

    /**
     * @param $formId
     * @return array
     */
    protected function _getActionOptions($formId)
    {
        $options = [];
        foreach ($this->_formTemplateList->toOptionArray() as $option) {
            if ($option['to_preview_menu']) {
                $options[] = [
                    'label'     => __('Preview as ' . $option['label']),
                    'onclick'   => 'window.open(\'' . $this->getPreviewUrl($formId, $option['value']) . '\')',
                ];
            }
        }

        return $options;
    }

    /**
     * @param $formId
     * @param $template
     * @return string
     */
    protected function getPreviewUrl($formId, $template)
    {
        return $this->_urlBuilder->getBaseUrl()
            . 'customform/form/preview'
            . '/id/' . $formId
            . '/form_display/' . $template;
    }
}
