<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Acr\Model;

use Aheadworks\Acr\Api\Data\QueueInterface;
use Aheadworks\Acr\Api\Data\QueueExtensionInterface;
use Aheadworks\Acr\Model\ResourceModel\Queue as QueueResource;
use Magento\Framework\Model\AbstractModel;

/**
 * Class Queue
 * @package Aheadworks\Acr\Model
 */
class Queue extends AbstractModel implements QueueInterface
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(QueueResource::class);
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->getData(self::ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setId($queueId)
    {
        return $this->setData(self::ID, $queueId);
    }

    /**
     * {@inheritdoc}
     */
    public function getRuleId()
    {
        return $this->getData(self::RULE_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setRuleId($ruleId)
    {
        return $this->setData(self::RULE_ID, $ruleId);
    }

    /**
     * {@inheritdoc}
     */
    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }

    /**
     * {@inheritdoc}
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * {@inheritdoc}
     */
    public function getCreatedAt()
    {
        return $this->getData(self::CREATED_AT);
    }

    /**
     * {@inheritdoc}
     */
    public function setCreatedAt($createdAt)
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }

    /**
     * {@inheritdoc}
     */
    public function getScheduledAt()
    {
        return $this->getData(self::SCHEDULED_AT);
    }

    /**
     * {@inheritdoc}
     */
    public function setScheduledAt($scheduledAt)
    {
        return $this->setData(self::SCHEDULED_AT, $scheduledAt);
    }

    /**
     * {@inheritdoc}
     */
    public function getSentAt()
    {
        return $this->getData(self::SENT_AT);
    }

    /**
     * {@inheritdoc}
     */
    public function setSentAt($sentAt)
    {
        return $this->setData(self::SENT_AT, $sentAt);
    }

    /**
     * {@inheritdoc}
     */
    public function getStoreId()
    {
        return $this->getData(self::STORE_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setStoreId($storeId)
    {
        return $this->setData(self::STORE_ID, $storeId);
    }

    /**
     * {@inheritdoc}
     */
    public function getRecipientName()
    {
        return $this->getData(self::RECIPIENT_NAME);
    }

    /**
     * {@inheritdoc}
     */
    public function setRecipientName($name)
    {
        return $this->setData(self::RECIPIENT_NAME, $name);
    }

    /**
     * {@inheritdoc}
     */
    public function getRecipientEmail()
    {
        return $this->getData(self::RECIPIENT_EMAIL);
    }

    /**
     * {@inheritdoc}
     */
    public function setRecipientEmail($email)
    {
        return $this->setData(self::RECIPIENT_EMAIL, $email);
    }

    /**
     * {@inheritdoc}
     */
    public function getCartHistoryId()
    {
        return $this->getData(self::CART_HISTORY_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setCartHistoryId($cartHistoryId)
    {
        return $this->setData(self::CART_HISTORY_ID, $cartHistoryId);
    }

    /**
     * {@inheritdoc}
     */
    public function getSavedSubject()
    {
        return $this->getData(self::SAVED_SUBJECT);
    }

    /**
     * {@inheritdoc}
     */
    public function setSavedSubject($subject)
    {
        return $this->setData(self::SAVED_SUBJECT, $subject);
    }

    /**
     * {@inheritdoc}
     */
    public function getSavedContent()
    {
        return $this->getData(self::SAVED_CONTENT);
    }

    /**
     * {@inheritdoc}
     */
    public function setSavedContent($content)
    {
        return $this->setData(self::SAVED_CONTENT, $content);
    }

    /**
     * {@inheritdoc}
     */
    public function getExtensionAttributes()
    {
        return $this->getData(self::EXTENSION_ATTRIBUTES_KEY);
    }

    /**
     * {@inheritdoc}
     */
    public function setExtensionAttributes(QueueExtensionInterface $extensionAttributes)
    {
        return $this->setData(self::EXTENSION_ATTRIBUTES_KEY, $extensionAttributes);
    }
}
