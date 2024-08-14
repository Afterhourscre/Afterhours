<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Faq\Controller\Article;

use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\Redirect;
use Aheadworks\Faq\Controller\AbstractAction;
use Magento\Store\Model\StoreManagerInterface;
use Aheadworks\Faq\Model\Config;
use Aheadworks\Faq\Model\Email\Notifier;
use Aheadworks\Faq\Model\Article\Validator;

class Question extends AbstractAction
{
    /**
     * @var Notifier
     */
    private $notifier;

    /**
     * @var Validator
     */
    private $validator;

    /**
     * @param Context $context
     * @param StoreManagerInterface $storeManager
     * @param Config $config
     * @param Notifier $notifier
     * @param Validator $validator
     */
    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        Config $config,
        Notifier $notifier,
        Validator $validator
    ) {
        parent::__construct($context, $storeManager);
        $this->notifier = $notifier;
        $this->config = $config;
        $this->validator = $validator;
    }

    /**
     * Post user question
     *
     * @return Redirect
     */
    public function _execute()
    {
        $data = $this->getRequest()->getParams();
        $errors = $this->validator->validateQuestionFormData($data);
        if (!empty($errors)) {
            foreach ($errors as $error) {
                $this->messageManager->addErrorMessage($error);
            }
        } else {
            try {
                $result = $this->sendNewQuestionEmail($data);
                if ($result) {
                    $this->messageManager->addSuccessMessage(
                        __('Thanks for your question. We\'ll respond to you very soon.')
                    );
                } else {
                    $this->messageManager->addErrorMessage('Unable to send mail.');
                }
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(
                    __('Something went wrong. Please try again later.')
                );
            }
        }
        return $this->resultRedirectFactory->create()->setUrl($this->_redirect->getRedirectUrl());
    }

    /**
     * Send email with question
     *
     * @param array $questionData
     * @return bool
     */
    private function sendNewQuestionEmail($questionData)
    {
        $result = $this->notifier->notifyAdminAboutNewQuestion($questionData);
        return $result;
    }
}
