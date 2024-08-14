<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Faq\Model\Email;

use Aheadworks\Faq\Model\Email\Sender;
use Magento\Framework\Exception\MailException;
use Psr\Log\LoggerInterface;
use Aheadworks\Faq\Model\Email\Processor as EmailProcessor;

/**
 * Class Notifier
 * @package Aheadworks\Faq\Model\Email
 */
class Notifier
{
    /**
     * @var Sender
     */
    private $sender;

    /**
     * @var EmailProcessor
     */
    private $emailProcessor;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param Sender $sender
     * @param EmailProcessor $emailProcessor
     * @param LoggerInterface $logger
     */
    public function __construct(
        Sender $sender,
        EmailProcessor $emailProcessor,
        LoggerInterface $logger
    ) {
        $this->sender = $sender;
        $this->emailProcessor = $emailProcessor;
        $this->logger = $logger;
    }

    /**
     * Notify about ticket activated
     *
     * @param array $formData
     * @return bool
     */
    public function notifyAdminAboutNewQuestion($formData)
    {
        $emailMetadata = $this->emailProcessor->process($formData);
        try {
            $this->sender->send($emailMetadata);
        } catch (MailException $e) {
            $this->logger->critical($e);
            return false;
        }

        return true;
    }
}
