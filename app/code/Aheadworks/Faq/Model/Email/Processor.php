<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Faq\Model\Email;

use Aheadworks\Faq\Model\Config;
use Aheadworks\Faq\Model\Email\EmailMetadataInterface;
use Aheadworks\Faq\Model\Email\EmailMetadataInterfaceFactory;
use Magento\Framework\App\Area;
use Magento\Store\Model\StoreManagerInterface;
use Aheadworks\Faq\Model\Email\Processors\ProcessorInterface;

/**
 * Class Processor
 * @package Aheadworks\Faq\Model\Email
 */
class Processor
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var EmailMetadataInterfaceFactory
     */
    private $emailMetadataFactory;

    /**
     * @var ProcessorInterface[]
     */
    private $variablesProcessors;

    /**
     * Processor constructor.
     * @param Config $config
     * @param StoreManagerInterface $storeManager
     * @param EmailMetadataInterfaceFactory $emailMetadataFactory
     * @param array $variablesProcessors
     */
    public function __construct(
        Config $config,
        StoreManagerInterface $storeManager,
        EmailMetadataInterfaceFactory $emailMetadataFactory,
        array $variablesProcessors = []
    ) {
        $this->config = $config;
        $this->storeManager = $storeManager;
        $this->emailMetadataFactory = $emailMetadataFactory;
        $this->variablesProcessors = $variablesProcessors;
    }

    /**
     * Process
     *
     * @param array $data
     * @return EmailMetadataInterface
     */
    public function process($data)
    {
        return $this->getMetadata($data);
    }

    /**
     * Retrieve metadata
     *
     * @param array $data
     * @return EmailMetadataInterface
     */
    private function getMetadata($data)
    {
        $storeId = $this->storeManager->getStore()->getId();
        /** @var EmailMetadataInterface $emailMetaData */
        $emailMetaData = $this->emailMetadataFactory->create();
        $emailMetaData
            ->setTemplateId($this->getTemplateId())
            ->setTemplateOptions($this->getTemplateOptions($storeId))
            ->setTemplateVariables($this->prepareTemplateVariables($data))
            ->setSenderName($this->getSenderName($data))
            ->setSenderEmail($this->getSenderEmail($data))
            ->setRecipientName($this->getRecipientName())
            ->setRecipientEmail($this->getRecipientEmail());

        return $emailMetaData;
    }

    /**
     * Retrieve template id
     *
     * @return string
     */
    private function getTemplateId()
    {
        return Config::DEFAULT_EMAIL_TEMPLATE;
    }

    /**
     * Retrieve recipient name
     *
     * @return string
     */
    private function getRecipientName()
    {
        return '';
    }

    /**
     * Retrieve recipient email
     *
     * @return string
     */
    private function getRecipientEmail()
    {
        return $this->config->getQuestionEmail();
    }

    /**
     * Retrieve sender name
     *
     * @param array $data
     * @return string
     */
    private function getSenderName($data)
    {
        return $data['name'];
    }

    /**
     * Retrieve sender email
     *
     * @param array $data
     * @return string
     */
    private function getSenderEmail($data)
    {
        return $data['email'];
    }

    /**
     * Prepare template options
     *
     * @param int $storeId
     * @return array
     */
    private function getTemplateOptions($storeId)
    {
        return [
            'area' => Area::AREA_FRONTEND,
            'store' => $storeId
        ];
    }

    /**
     * Prepare template variables
     *
     * @param array $data
     * @return array
     */
    private function prepareTemplateVariables($data)
    {
        $templateVariables = $data;

        foreach ($this->variablesProcessors as $processor) {
            $templateVariables = $processor->prepareVariables($templateVariables);
        }

        return $templateVariables;
    }
}
