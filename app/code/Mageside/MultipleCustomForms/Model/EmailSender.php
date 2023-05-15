<?php
/**
 * Copyright © Mageside. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */
namespace Mageside\MultipleCustomForms\Model;

use Mageside\MultipleCustomForms\Model\CustomForm\Field;
use Magento\Framework\App\Filesystem\DirectoryList;

class EmailSender
{

    const XML_PATH_EMAIL_TEMPLATE = 'mageside_customform/general/email_template';

    const XML_PATH_EMAIL_SENDER = 'contact/email/sender_email_identity';

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $_request;

    /**
     * @var \Magento\Framework\Translate\Inline\StateInterface
     */
    protected $_inlineTranslation;

    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    protected $_transportBuilder;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $_filesystem;

    /**
     * @var ResourceModel\RecipientDependency\CollectionFactory
     */
    protected $recipientCollectionFactory;

    /**
     * @var ResourceModel\RecipientDependency\CollectionFactory
     */
    protected $dependencyCollectionFactory;

    /**
     * @var ResourceModel\Recipient
     */
    protected $recipientModel;

    /**
     * Escaper
     *
     * @var \Magento\Framework\Escaper
     */
    protected $_escaper;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * EmailSender constructor.
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation
     * @param UploadTransportBuilder $transportBuilder
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Filesystem $filesystem
     * @param ResourceModel\Recipient\CollectionFactory $recipientCollectionFactory
     * @param ResourceModel\RecipientDependency\CollectionFactory $dependencyCollectionFactory
     * @param ResourceModel\Recipient $recipientModel
     * @param \Magento\Framework\Escaper $escaper
     */
    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        UploadTransportBuilder $transportBuilder,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Filesystem $filesystem,
        \Mageside\MultipleCustomForms\Model\ResourceModel\Recipient\CollectionFactory $recipientCollectionFactory,
        \Mageside\MultipleCustomForms\Model\ResourceModel\RecipientDependency\CollectionFactory $dependencyCollectionFactory,
        \Mageside\MultipleCustomForms\Model\ResourceModel\Recipient $recipientModel,
        \Magento\Framework\Escaper $escaper,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->_request = $request;
        $this->_inlineTranslation = $inlineTranslation;
        $this->_transportBuilder = $transportBuilder;
        $this->_scopeConfig = $scopeConfig;
        $this->_filesystem = $filesystem;
        $this->recipientCollectionFactory = $recipientCollectionFactory;
        $this->dependencyCollectionFactory = $dependencyCollectionFactory;
        $this->recipientModel = $recipientModel;
        $this->_escaper = $escaper;
        $this->_storeManager = $storeManager;
    }

    /**
     * @param $form
     * @param $data
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\MailException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function send($form, $data)
    {
        $emails = $this->getEmails($form, $data);
        if (empty($emails)) {
            return $this;
        }

        $this->_inlineTranslation->suspend();
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $this->_transportBuilder
            ->setTemplateIdentifier(
                $this->_scopeConfig->getValue(self::XML_PATH_EMAIL_TEMPLATE, $storeScope)
            )
            ->setTemplateOptions(
                [
                    'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                    'store' => $this->_storeManager->getStore()->getId(),
                ]
            )
            ->setTemplateVars($this->prepareEmailVars($form, $data))
            ->setFrom(
                $this->_scopeConfig->getValue(self::XML_PATH_EMAIL_SENDER, $storeScope)
            )
            ->addTo($emails);

        $this->_processAttachments($form, $data);

        $this->_transportBuilder->getTransport()->sendMessage();
        $this->_inlineTranslation->resume();

        return $this;
    }

    /**
     * @param $form
     * @param $data
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getEmails($form, $data)
    {
        $emails = [];
        $formFields = $form->getFields();
        $storeId = $this->_storeManager->getStore()->getId();
        $emailsByDependency = $this->recipientModel->getEmailsByDependency($formFields, $form->getId(), $data, $storeId);
        $emailsCommon = $this->recipientModel->getCommonEmails($form->getId(), $storeId);;
        $this->prepareEmailList($emails, $emailsByDependency);
        $this->prepareEmailList($emails, $emailsCommon);

        return $emails;
    }

    /**
     * @param $emails
     * @param $recipientData
     */
    private function prepareEmailList(&$emails, $recipientData)
    {
        foreach ($recipientData as $data) {
            $emailsRaw = array_map(
                function($value) {
                    return trim($value);
                },
                explode(',', $data['emails'])
            );

            foreach ($emailsRaw as $email) {
                if (array_search($email, $emails) === FALSE) {
                    $emails[] = $email;
                }
            }
        }
    }

    /**
     * @param $form
     * @param $data
     * @return array
     */
    protected function prepareEmailVars($form, $data)
    {
        $content = '';
        /** @var \Mageside\MultipleCustomForms\Model\CustomForm $form */
        $formFieldsData = $form->getFieldCollection();
        foreach ($formFieldsData as $field) {
            /** @var \Mageside\MultipleCustomForms\Model\CustomForm\Field $field */
            $value = $field->getSubmittedValue($data);
            $value = !empty($value) ? $field->getFieldOutput($value) : '&nbsp;';
            $content .= "<b>" .
                $this->_escaper->escapeHtml($field->getTitle()) .
                "</b>:" .
                "\t" .
                $this->_escaper->escapeHtml($value) .
                "<br>";
        }

        $vars = [
            'form_name' => $form->getName(),
            'content'   => $content,
            'subject'   => $form->getSubjectEmail()
        ];

        return $vars;
    }

    /**
     * @param $form
     * @param $data
     */
    protected function _processAttachments($form, $data)
    {
        $directory = $form->getSubmissionType() == 'both'
            ? FileUploader::SUBMISSION_DIRECTORY
            : FileUploader::TEMP_DIRECTORY;

        $mediaDirectory = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA);

        foreach ($form->getFieldCollection() as $field) {
            /** @var \Mageside\MultipleCustomForms\Model\CustomForm\Field $field */
            if ($field->getType() == 'file') {
                $files = explode(
                    ",",
                    $data[Field::FIELD_PREFIX . $field->getId()]
                );
                foreach ($files as $file) {
                    if ($mediaDirectory->isFile($directory . '/' . $file)) {
                        $path = $mediaDirectory->getAbsolutePath($directory . '/' . $file);
                        $this->_transportBuilder->addAttachment($path, $file);
                    }
                }
            }
        }
    }
}
