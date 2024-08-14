<?php
/**
 * Copyright © Mageside. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */
namespace Mageside\MultipleCustomForms\Block\Adminhtml\Submission;

class View extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Mageside\MultipleCustomForms\Model\Submission
     */
    protected $_submissionForm;

    /**
     * @var \Mageside\MultipleCustomForms\Model\CustomForm
     */
    protected $_customForm;

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $_filesystem;

    /**
     * Submit constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Mageside\MultipleCustomForms\Model\Submission $submissionForm
     * @param \Mageside\MultipleCustomForms\Model\CustomForm $customForm
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Mageside\MultipleCustomForms\Model\Submission $submissionForm,
        \Mageside\MultipleCustomForms\Model\CustomForm $customForm
    ) {
        $this->_submissionForm = $submissionForm;
        $this->_customForm = $customForm;
        $this->_filesystem = $context->getFilesystem();
        parent::__construct($context);
    }

    protected function _prepareLayout()
    {
        if ($toolbar = $this->getLayout()->getBlock('page.actions.toolbar')) {
            $toolbar->addChild(
                'add_button',
                'Magento\Backend\Block\Widget\Button',
                [
                    'label' => __('Back'),
                    'class' => 'back',
                    'onclick' => 'setLocation(\''
                        . $this->getUrl(
                            'customform/form_submission/manage',
                            ['id' => $this->getSubmission()->getFormId()]
                        )
                        .  '\')',
                ]
            );
        }

        return parent::_prepareLayout();
    }

    /**
     * @return bool
     */
    public function isInformationAvailable()
    {
        return $this->getSubmission()->getId() && $this->getForm()->getId();
    }

    /**
     * @return mixed
     */
    public function getSubmissionId()
    {
        return $this->_request->getParam('id');
    }

    /**
     * @return \Mageside\MultipleCustomForms\Model\Submission
     */
    public function getSubmission()
    {
        if (!$this->_submissionForm->getId()) {
            $this->_submissionForm->load($this->getSubmissionId());
        }

        return $this->_submissionForm;
    }

    /**
     * @return mixed
     */
    public function getFormFieldCollection()
    {
        return $this->getForm()
            ->getFieldCollectionWithSubmissionData($this->getSubmissionId());
    }

    /**
     * @return \Mageside\MultipleCustomForms\Model\CustomForm
     */
    public function getForm()
    {
        if (!$this->_customForm->getId()) {
            $this->_customForm->load($this->getSubmission()->getFormId());
        }

        return $this->_customForm;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return __(
            '%formName (submitted at: %submitted)',
            [
                'formName' => $this->getForm()->getName(),
                'submitted' => $this->getSubmission()->getCreatedAt()
            ]
        );
    }

    /**
     * @param $fileName
     * @return string
     */
    public function getContentUrl($fileName)
    {
        return $this->getUrl('customform/field/getcontent', ['file' => $fileName]);
    }

    /**
     * @param $field
     * @return array
     */
    public function getFiles($field)
    {
        if ($files = $field->getSubmissionValue()) {
            return explode(',', $files);
        }

        return [];
    }

    /**
     * @param $file
     * @return bool
     */
    public function isFileImage($file)
    {
        return (bool) preg_match('/.(jpg|jpeg|png|gif)/i', $this->getContentUrl($file));
    }
}
