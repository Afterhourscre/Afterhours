<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Acr\Api;

use Aheadworks\Acr\Api\Data\RuleInterface;
use Aheadworks\Acr\Api\Data\CartHistoryInterface;

/**
 * Interface RuleManagementInterface
 * @package Aheadworks\Acr\Api
 */
interface RuleManagementInterface
{
    /**
     * Validate and return valid rules
     *
     * @param \Aheadworks\Acr\Api\Data\CartHistoryInterface $cartHistory
     * @return \Aheadworks\Acr\Api\Data\RuleSearchResultsInterface
     */
    public function validate(CartHistoryInterface $cartHistory);

    /**
     * Get email send time
     *
     * @param \Aheadworks\Acr\Api\Data\RuleInterface $rule
     * @param string $triggerTime
     * @return string
     */
    public function getEmailSendTime(RuleInterface $rule, $triggerTime);

    /**
     * Get email preview
     *
     * @param int $storeId
     * @param string $subject
     * @param string $content
     * @return \Aheadworks\Acr\Model\Preview
     */
    public function getPreview($storeId, $subject, $content);
}
