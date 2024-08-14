<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Acr\Api;

use Aheadworks\Acr\Api\Data\QueueInterface;
use Aheadworks\Acr\Api\Data\RuleInterface;
use Aheadworks\Acr\Api\Data\CartHistoryInterface;

interface QueueManagementInterface
{
    /**
     * Add new email to queue
     *
     * @param \Aheadworks\Acr\Api\Data\RuleInterface $rule
     * @param \Aheadworks\Acr\Api\Data\CartHistoryInterface $cartHistory
     * @return bool
     */
    public function add(RuleInterface $rule, CartHistoryInterface $cartHistory);

    /**
     * Cancel email
     *
     * @param \Aheadworks\Acr\Api\Data\QueueInterface $queue
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException If validation fails
     */
    public function cancel(QueueInterface $queue);

    /**
     * Cancel email by id
     *
     * @param int $queueId
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException If validation fails
     */
    public function cancelById($queueId);

    /**
     * Send email
     *
     * @param \Aheadworks\Acr\Api\Data\QueueInterface $queue
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function send(QueueInterface $queue);

    /**
     * Send test email
     *
     * @param \Aheadworks\Acr\Api\Data\RuleInterface $rule
     * @return bool
     */
    public function sendTest(RuleInterface $rule);

    /**
     * Send email by id
     *
     * @param int $queueId
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function sendById($queueId);

    /**
     * Get email preview
     *
     * @param \Aheadworks\Acr\Api\Data\QueueInterface $queue
     * @return \Aheadworks\Acr\Model\Preview
     */
    public function getPreview(QueueInterface $queue);

    /**
     * Clear queue
     *
     * @param int $keepForDays
     * @return bool
     */
    public function clearQueue($keepForDays);

    /**
     * Send scheduled emails
     *
     * @param int|null $timestamp
     * @return bool
     */
    public function sendScheduledEmails($timestamp = null);
}
