<?php
/**
 * Copyright © Mageside. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */
namespace Mageside\MultipleCustomForms\Controller\Form;

use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\App\RequestInterface;
use Magento\Store\Model\ScopeInterface;

class Post extends \Magento\Framework\App\Action\Action
{
    /**
     * Enabled config path
     */
    const XML_PATH_ENABLED = 'mageside_customform/general/enabled';

    /**
     * @var \Magento\Framework\Data\Form\FormKey\Validator
     */
    protected $formKeyValidator;

    /**
     * @var \Mageside\MultipleCustomForms\Model\SubmissionFactory
     */
    protected $_submissionFactory;

    /**
     * @var \Mageside\MultipleCustomForms\Model\CustomFormFactory
     */
    protected $_customFormFactory;

    /**
     * @var \Mageside\MultipleCustomForms\Model\EmailSender
     */
    protected $_emailSender;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Magento\Framework\View\LayoutFactory
     */
    protected $_layoutFactory;

    /**
     * Post constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator
     * @param \Mageside\MultipleCustomForms\Model\SubmissionFactory $submissionFactory
     * @param \Mageside\MultipleCustomForms\Model\CustomFormFactory $customFormFactory
     * @param \Mageside\MultipleCustomForms\Model\EmailSender $emailSender
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\View\LayoutFactory $layoutFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        \Mageside\MultipleCustomForms\Model\SubmissionFactory $submissionFactory,
        \Mageside\MultipleCustomForms\Model\CustomFormFactory $customFormFactory,
        \Mageside\MultipleCustomForms\Model\EmailSender $emailSender,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\View\LayoutFactory $layoutFactory
    ) {
        $this->formKeyValidator = $formKeyValidator;
        $this->_submissionFactory = $submissionFactory;
        $this->_customFormFactory = $customFormFactory;
        $this->_emailSender = $emailSender;
        $this->_scopeConfig = $scopeConfig;
        $this->_layoutFactory = $layoutFactory;

        parent::__construct($context);
    }

    public function execute()
    {
        $form = null;
        $errors = false;

        if (!$this->formKeyValidator->validate($this->getRequest())) {
            $this->addMessage('Invalid form data.', 'error', $form);

            return $this->getResult($form);
        }

        if ($formId = $this->_request->getParam('form_id')) {
            /** @var \Mageside\MultipleCustomForms\Model\CustomForm $form */
            $form = $this->_customFormFactory->create()->load($formId);

            $params = $this->prepareParams();
            if (!$form->validateData($params)) {
                $this->addMessage('Invalid form data.', 'error', $form);

                return $this->getResult($form);
            }

            $submissionType = $form->getSubmissionType();

            if ($submissionType == 'db' || $submissionType == 'both') {
                try {
                    /** @var \Mageside\MultipleCustomForms\Model\Submission $submission */
                    $submission = $this->_submissionFactory->create();
                    $submission->setFormModel($form);
                    $submission->setData($params);
                    $submission->save();
                    $params = $submission->getData();
                } catch (\Exception $exception) {
                    $errors = true;
                }
            }

            if ($submissionType == 'email' || $submissionType == 'both') {
                try {
                    $this->_emailSender->send($form, $params);
                } catch (\Exception $exception) {
                    $errors = true;
                }
            }

            if ($errors) {
                $this->addMessage('Something went wrong while sending request. Please contact us.', 'error', $form);
            } else {
                $this->addMessage('The request successfully saved.', 'success', $form);
            }
        }

        return $this->getResult($form);
    }

    /**
     * @param null $form
     * @return \Magento\Framework\Controller\Result\Redirect|\Magento\Framework\Controller\Result\Json
     */
    protected function getResult($form = null)
    {
        $redirectUrl = '/';
        $clearMessages = true;
        if ($form) {
            $redirectUrl = $form->getData('redirect_url') ? : $redirectUrl;
            $clearMessages = $form->getData('after_submit') == 'redirect' ? false : true;
        }

        if ($this->getRequest()->isAjax()) {
            $hasError = (bool)$this->messageManager->getMessages()->getCountByType(
                \Magento\Framework\Message\MessageInterface::TYPE_ERROR
            );

            /** @var $block \Magento\Framework\View\Element\Messages */
            $block = $this->_layoutFactory->create()->getMessagesBlock();
            $block->setMessages($this->messageManager->getMessages($clearMessages));
            $messages = $block->getGroupedHtml();

            $resultJson = $this->resultFactory
                ->create(\Magento\Framework\Controller\ResultFactory::TYPE_JSON);

            return $resultJson->setData(
                [
                    'messages'  => $messages,
                    'error'     => $hasError
                ]
            );
        }

        $resultRedirect = $this->resultRedirectFactory->create();

        return $resultRedirect->setPath($redirectUrl);
    }

    /**
     * @return array
     */
    protected function prepareParams()
    {
        $params = $this->_request->getParams();
        $params['remote_ip'] = $this->_request->getServer('REMOTE_ADDR');
        $params['hostname'] = $this->_request->getServer('SERVER_NAME');

        return $params;
    }

    /**
     * @param $message
     * @param $type
     * @param null $form
     */
    protected function addMessage($message, $type, $form = null)
    {
        $successMessage = '';
        $failMessage = '';
        if ($form && $form->getId()) {
            $successMessage = $form->getSuccessMessage();
            $failMessage = $form->getFailMessage();
        }

        if ($type == 'success') {
            $message = $successMessage ? $successMessage : $message;
            $this->messageManager->addSuccessMessage(__($message));
        } elseif ($type == 'error') {
            $message = $failMessage ? $failMessage : $message;
            $this->messageManager->addErrorMessage(__($message));
        }
    }

    /**
     * @param RequestInterface $request
     * @return \Magento\Framework\App\ResponseInterface
     * @throws NotFoundException
     */
    public function dispatch(RequestInterface $request)
    {
        if (!$this->_scopeConfig->isSetFlag(self::XML_PATH_ENABLED, ScopeInterface::SCOPE_STORE)) {
            throw new NotFoundException(__('Page not found.'));
        }
        return parent::dispatch($request);
    }
}
