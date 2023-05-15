<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Faq\Model\Email;

use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Translate\Inline\StateInterface;

/**
 * Class Sender
 * @package Aheadworks\Faq\Model\Email
 */
class Sender
{
    /**
     * @var TransportBuilder
     */
    private $transportBuilder;

    /**
     * @var StateInterface
     */
    private $inlineTranslation;

    /**
     * @param TransportBuilder $transportBuilder
     * @param StateInterface $inlineTranslation
     */
    public function __construct(
        TransportBuilder $transportBuilder,
        StateInterface $inlineTranslation
    ) {
        $this->transportBuilder = $transportBuilder;
        $this->inlineTranslation = $inlineTranslation;
    }

    /**
     * Send email message
     *
     * @param EmailMetadataInterface $emailMetadata
     * @throws \Magento\Framework\Exception\MailException
     */
    public function send($emailMetadata)
    {
        $this->inlineTranslation->suspend();
        try {
            $this->transportBuilder
                ->setTemplateIdentifier($emailMetadata->getTemplateId())
                ->setTemplateOptions($emailMetadata->getTemplateOptions())
                ->setTemplateVars($emailMetadata->getTemplateVariables())
                ->setFrom(['name' => $emailMetadata->getSenderName(), 'email' => $emailMetadata->getSenderEmail()])
                ->addTo($emailMetadata->getRecipientEmail(), $emailMetadata->getRecipientName());

            $this->transportBuilder->getTransport()->sendMessage();
        } finally {
            $this->inlineTranslation->resume();
        }
    }
}
