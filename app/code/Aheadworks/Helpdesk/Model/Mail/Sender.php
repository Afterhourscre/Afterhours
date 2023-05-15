<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Helpdesk\Model\Mail;

use Aheadworks\Helpdesk\Model\Config;
use Magento\Framework\Exception\MailException;
use Magento\Framework\Message\ManagerInterface as MessageManagerInterface;
use Magento\Framework\Mail\Template\TransportBuilder;
use Aheadworks\Helpdesk\Model\Mail\Template as HelpdeskMailTemplate;

/**
 * Class Sender
 * @package Aheadworks\Helpdesk\Model\Mail
 */
class Sender
{
    /**
     * Const for email template to add replies history marker
     */
    const ADD_HDU_REPLIES_HISTORY_MARKER_FLAG_NAME = 'add_hdu_replies_history_marker_flag';

    /**
     * @var TransportBuilder
     */
    private $transportBuilder;

    /**
     * @var MessageManagerInterface
     */
    private $messageManager;

    /**
     * @param TransportBuilder $transportBuilder
     * @param MessageManagerInterface $messageManager
     */
    public function __construct(
        TransportBuilder $transportBuilder,
        MessageManagerInterface $messageManager
    ) {
        $this->transportBuilder = $transportBuilder;
        $this->messageManager = $messageManager;
    }

    /**
     * Send email
     * @param array $emailData
     * @param bool $needReplyTo
     */
    public function sendEmail($emailData, $needReplyTo = true)
    {
        $emailData[self::ADD_HDU_REPLIES_HISTORY_MARKER_FLAG_NAME] = true;

        $this->transportBuilder
            ->setTemplateModel(HelpdeskMailTemplate::class)
            ->setTemplateIdentifier($emailData['template_id'])
            ->setTemplateOptions([
                'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                'store' => $emailData['store_id']
            ])
            ->setTemplateVars($emailData)
            ->setFrom($emailData['from'])
            ->addTo($emailData['to'], $emailData['sender_name'])
        ;
        if (isset($emailData['cc_recipients'])) {
            $this->transportBuilder->addCc($emailData['cc_recipients']);
        }
        if ($needReplyTo && isset($emailData['gateway'])) {
            $this->transportBuilder->setReplyTo($emailData['gateway']);
        }
        $transport = $this->transportBuilder->getTransport();
        try {
            $transport->sendMessage();
        } catch (MailException $e) {
            $this->messageManager->addErrorMessage($e->getMessage(), Config::EMAIL_ERROR_MESSAGE_GROUP);
        }
    }
}
