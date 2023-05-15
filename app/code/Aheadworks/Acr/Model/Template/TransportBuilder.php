<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Acr\Model\Template;

use Magento\Framework\Mail\MessageInterface;

/**
 * Class TransportBuilder
 * @package Aheadworks\Acr\Model\Template
 */
class TransportBuilder extends \Magento\Framework\Mail\Template\TransportBuilder
{
    /**
     * Template data
     *
     * @var array
     */
    private $templateData = [];

    /**
     * @var string
     */
    private $messageType = MessageInterface::TYPE_HTML;

    /**
     * @var \Zend_Mime_Part|string
     */
    private $content;

    /**
     * @var string
     */
    private $subject;

    /**
     * Set template data
     *
     * @param array $data
     * @return $this
     */
    public function setTemplateData($data)
    {
        $this->templateData = $data;
        return $this;
    }

    /**
     * Set message type
     *
     * @param string $messageType
     * @return $this
     */
    public function setMessageType($messageType)
    {
        $this->messageType = $messageType;
        return $this;
    }

    /**
     * Get message content
     *
     * @return string
     */
    public function getMessageContent()
    {
        if ($this->content instanceof \Zend_Mime_Part) {
            return $this->content->getRawContent();
        } else if ($this->content instanceof \Zend\Mime\Message) {
            return $this->content->generateMessage();
        } else {
            return $this->content;
        }
    }

    /**
     * Get message subject
     *
     * @return string
     */
    public function getMessageSubject()
    {
        return $this->subject;
    }

    /**
     * Prepare message
     *
     * @return $this
     */
    protected function prepareMessage()
    {
        $template = $this->getTemplate()->setData($this->templateData);

        $this->message->setMessageType(
            $this->messageType
        )->setBody(
            $template->getProcessedTemplate($this->templateVars)
        )->setSubject(
            $template->getSubject()
        );
        $this->content = $this->message->getBody();
        $this->subject = $template->getSubject();

        return $this;
    }

    /**
     * Prepare message for preview
     *
     * @return $this
     */
    public function prepareForPreview()
    {
        return $this->prepareMessage();
    }
    
    /**
     * Add from address
     *
     * @param string $senderEmail
     * @param string $senderName
     * @return $this
     */
    public function addFrom($senderEmail, $senderName)
    {
        $this->message->setFrom($senderEmail, $senderName);
        return $this;
    }
}
