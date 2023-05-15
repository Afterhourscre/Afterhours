<?php
/**
 * Copyright © Mageside. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */
namespace Mageside\MultipleCustomForms\Model;

class UploadTransportBuilder extends \Magento\Framework\Mail\Template\TransportBuilder
{
    protected $attachments = [];

    /**
     * @param $path
     * @param $name
     * @return $this
     * @see https://docs.zendframework.com/zend-mail/message/attachments/
     */
    public function addAttachment($path, $name)
    {
        if ($this->message instanceof \Zend_Mail) {
            $this->message->createAttachment(
                file_get_contents($path),
                \Zend_Mime::TYPE_OCTETSTREAM,
                \Zend_Mime::DISPOSITION_ATTACHMENT,
                \Zend_Mime::ENCODING_BASE64,
                $name
            );
        } else {
            $attachment = new \Zend\Mime\Part(file_get_contents($path));
            $attachment->setType(mime_content_type($path));
            $attachment->setFileName($name);
            $attachment->setDisposition(\Zend\Mime\Mime::DISPOSITION_ATTACHMENT);
            $attachment->setEncoding(\Zend\Mime\Mime::ENCODING_BASE64);
            $this->attachments[] = $attachment;
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    protected function prepareMessage()
    {
        parent::prepareMessage();

        if (!$this->message instanceof \Zend_Mail) {
            if (!empty($this->attachments)) {
                $body = $this->message->getBody();
                foreach ($this->attachments as $attachment) {
                    $body->addPart($attachment);
                }
                $this->message->setBody($body);
            }
        }

        return $this;
    }
}
