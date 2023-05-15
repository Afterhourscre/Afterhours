<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Acr\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface QueueInterface
 * @package Aheadworks\Acr\Api\Data
 */
interface QueueInterface extends ExtensibleDataInterface
{
    /**#@+
     * Constants defined for keys of the data array.
     * Identical to the name of the getter in snake case
     */
    const ID                    = 'id';
    const RULE_ID               = 'rule_id';
    const STATUS                = 'status';
    const CREATED_AT            = 'created_at';
    const SCHEDULED_AT          = 'scheduled_at';
    const SENT_AT               = 'sent_at';
    const STORE_ID              = 'store_id';
    const RECIPIENT_NAME        = 'recipient_name';
    const RECIPIENT_EMAIL       = 'recipient_email';
    const CART_HISTORY_ID       = 'cart_history_id';
    const SAVED_SUBJECT         = 'saved_subject';
    const SAVED_CONTENT         = 'saved_content';
    /**#@-*/

    /**#@+
     * Status values
     */
    const STATUS_PENDING = 1;
    const STATUS_SENT = 2;
    const STATUS_FAILED = 3;
    const STATUS_CANCELLED = 4;
    /**#@-*/

    /**
     * Get queue ID
     *
     * @return int|null
     */
    public function getId();

    /**
     * Set queue ID
     *
     * @param int $queueId
     * @return $this
     */
    public function setId($queueId);

    /**
     * Get rule ID
     *
     * @return int
     */
    public function getRuleId();

    /**
     * Set rule ID
     *
     * @param int $ruleId
     * @return $this
     */
    public function setRuleId($ruleId);

    /**
     * Get status
     *
     * @return int
     */
    public function getStatus();

    /**
     * Set status
     *
     * @param int $status
     * @return $this
     */
    public function setStatus($status);

    /**
     * Get creation time
     *
     * @return string
     */
    public function getCreatedAt();

    /**
     * Set creation time
     *
     * @param string $createdAt
     * @return $this
     */
    public function setCreatedAt($createdAt);

    /**
     * Get scheduled time
     *
     * @return string
     */
    public function getScheduledAt();

    /**
     * Set scheduled time
     *
     * @param string $scheduledAt
     * @return $this
     */
    public function setScheduledAt($scheduledAt);

    /**
     * Get sent time
     *
     * @return string|null
     */
    public function getSentAt();

    /**
     * Set sent time
     *
     * @param string $sentAt
     * @return $this
     */
    public function setSentAt($sentAt);

    /**
     * Get store ID
     *
     * @return int
     */
    public function getStoreId();

    /**
     * Set store ID
     *
     * @param int $storeId
     * @return $this
     */
    public function setStoreId($storeId);

    /**
     * Get recipient name
     *
     * @return string
     */
    public function getRecipientName();

    /**
     * Set recipient name
     *
     * @param string $name
     * @return $this
     */
    public function setRecipientName($name);

    /**
     * Get recipient email
     *
     * @return string
     */
    public function getRecipientEmail();

    /**
     * Set recipient email
     *
     * @param string $email
     * @return $this
     */
    public function setRecipientEmail($email);

    /**
     * Get cart history ID
     *
     * @return int
     */
    public function getCartHistoryId();

    /**
     * Set cart history ID
     *
     * @param int $cartHistoryId
     * @return $this
     */
    public function setCartHistoryId($cartHistoryId);

    /**
     * Get saved subject
     *
     * @return string|null
     */
    public function getSavedSubject();

    /**
     * Set saved subject
     *
     * @param string $subject
     * @return $this
     */
    public function setSavedSubject($subject);

    /**
     * Get saved content
     *
     * @return string|null
     */
    public function getSavedContent();

    /**
     * Set saved content
     *
     * @param string $content
     * @return $this
     */
    public function setSavedContent($content);

    /**
     * Retrieve existing extension attributes object or create a new one
     *
     * @return \Aheadworks\Acr\Api\Data\QueueExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object
     *
     * @param \Aheadworks\Acr\Api\Data\QueueExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(QueueExtensionInterface $extensionAttributes);
}
