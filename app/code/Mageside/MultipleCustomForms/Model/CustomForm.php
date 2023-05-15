<?php
/**
 * Copyright © Mageside. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */
namespace Mageside\MultipleCustomForms\Model;

use Mageside\MultipleCustomForms\Model\ReCaptcha\Adapter as ReCaptchaAdapter;

class CustomForm extends \Magento\Framework\Model\AbstractModel
{
    const FORM_PREFIX = 'form_';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'custom_form';

    /**
     * Parameter name in event
     *
     * @var string
     */
    protected $_eventObject = 'custom_form';

    /**
     * @var \Mageside\MultipleCustomForms\Model\ResourceModel\CustomForm\Field\CollectionFactory
     */
    protected $_fieldCollectionFactory;

    /**
     * @var ResourceModel\CustomForm\Fieldset\CollectionFactory
     */
    protected $_fieldsetCollectionFactory;

    /**
     * @var \Mageside\MultipleCustomForms\Helper\Config
     */
    protected $_configHelper;

    /**
     * Customer session
     *
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $_messageManager;

    /**
     * @var ReCaptchaAdapter
     */
    protected $reCaptchaAdapter;

    /**
     * CustomForm constructor.
     * @param \Magento\Framework\Model\Context $context
     * @param ResourceModel\CustomForm\Field\CollectionFactory $fieldCollectionFactory
     * @param ResourceModel\CustomForm\Fieldset\CollectionFactory $fieldsetCollectionFactory
     * @param \Magento\Framework\Registry $registry
     * @param \Mageside\MultipleCustomForms\Helper\Config $configHelper
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param ReCaptchaAdapter $reCaptchaAdapter
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Mageside\MultipleCustomForms\Model\ResourceModel\CustomForm\Field\CollectionFactory $fieldCollectionFactory,
        \Mageside\MultipleCustomForms\Model\ResourceModel\CustomForm\Fieldset\CollectionFactory $fieldsetCollectionFactory,
        \Magento\Framework\Registry $registry,
        \Mageside\MultipleCustomForms\Helper\Config $configHelper,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        ReCaptchaAdapter $reCaptchaAdapter,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->_fieldCollectionFactory = $fieldCollectionFactory;
        $this->_fieldsetCollectionFactory = $fieldsetCollectionFactory;
        $this->_configHelper = $configHelper;
        $this->_customerSession = $customerSession;
        $this->_messageManager = $messageManager;
        $this->reCaptchaAdapter = $reCaptchaAdapter;

        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    protected function _construct()
    {
        $this->_init('Mageside\MultipleCustomForms\Model\ResourceModel\CustomForm');
    }

    /**
     * Get field collection
     *
     * @return \Mageside\MultipleCustomForms\Model\ResourceModel\CustomForm\Field\Collection
     */
    public function getFieldCollection()
    {
        $fields = $this->_fieldCollectionFactory->create()
            ->addFieldToFilter('form_id', $this->getId())
            ->addOrderByPosition()
            ->addOptionsData();

        return $fields;
    }

    /**
     * Get fieldset collection
     *
     * @return \Mageside\MultipleCustomForms\Model\ResourceModel\CustomForm\Fieldset\Collection
     */
    public function getFieldsetCollection()
    {
        $fieldsets = $this->_fieldsetCollectionFactory->create()
            ->addFieldToFilter('form_id', $this->getId())
            ->addOrderByPosition();

        return $fieldsets;
    }

    /**
     * @param $submissionId
     * @return mixed
     */
    public function getFieldCollectionWithSubmissionData($submissionId)
    {
        $fields = $this->_fieldCollectionFactory->create()
            ->addFieldToFilter('form_id', $this->getId())
            ->addOrderByPosition()
            ->addSubmissionDataToCollection($submissionId);

        return $fields;
    }

    protected function _afterLoad()
    {
        $this->getFields();

        return parent::_afterLoad();
    }

    /**
     * Get fields data array
     *
     * @return mixed
     */
    public function getFields()
    {
        if (!$this->hasData('fields')) {
            $fields = [];
            foreach ($this->getFieldCollection() as $field) {
                $fields[] = $field->toArray();
            }
            $this->setData('fields', $fields);
        }

        return $this->getData('fields');
    }

    /**
     * @param $data
     * @return bool
     */
    public function validateData($data)
    {
        // Check if form is exist on database
        if (!$this->getId()) {
            return false;
        }

        if (!$this->_validateFields($data)) {
            return false;
        }

        if (!$this->_validateReCaptcha($data)) {
            return false;
        }

        return true;
    }

    /**
     * @param $data
     * @return bool
     */
    protected function _validateFields($data)
    {
        $valid = true;

        /** @var \Mageside\MultipleCustomForms\Model\CustomForm\Field $field */
        foreach ($this->getFieldCollection() as $field) {
            $valid = !$field->validateData($data) ? false : $valid;
        }

        return $valid;
    }

    /**
     * @return bool
     */
    public function isReCaptchaEnabled()
    {
        if ($this->getRecaptcha() !== 'disabled') {
            if ($this->_customerSession->isLoggedIn()
                && $this->getRecaptcha() === 'only_for_guests'
            ) {
                return false;
            }
            return true;
        }

        return false;
    }

    /**
     * @param $data
     * @return bool
     */
    protected function _validateReCaptcha($data)
    {
        if (!$this->isReCaptchaEnabled()) {
            return true;
        }

        $isReCaptchaValid = false;
        if (empty($data['g-recaptcha-response-' . $this->getId()])) {
            return false;
        }

        try {
            $resp = $this->reCaptchaAdapter->verify($data['g-recaptcha-response-' . $this->getId()], $data['remote_ip']);
            if ($resp && $resp->isSuccess()) {
                // if Domain Name Validation turned off don't forget to check hostname field
                if ($resp->getHostName() === $data['hostname']) {
                    $isReCaptchaValid = true;
                }
            } else {
                $this->_messageManager->addErrorMessage(__('Unable to validate field reCAPTCHA. Please check configuration.'));
            }
        } catch (\Exception $e) {
            $this->_messageManager->addErrorMessage(__('Unable to validate field reCAPTCHA. Please check configuration.'));
        }

        return $isReCaptchaValid;
    }

    /**
     * @return mixed
     */
    public function getRedirect()
    {
        return $this->getRedirectUrl();
    }
}
