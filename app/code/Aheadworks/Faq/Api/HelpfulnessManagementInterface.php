<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */


namespace Aheadworks\Faq\Api;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\LocalizedException;
use Aheadworks\Faq\Api\Data\VoteResultInterface;

/**
 * FAQ helpfulness interface
 *
 * @api
 */
interface HelpfulnessManagementInterface
{
    /**
     * Like article
     *
     * @param int $articleId
     * @return VoteResultInterface
     * @throws LocalizedException
     */
    public function like($articleId);

    /**
     * Dislike article
     *
     * @param int $articleId
     * @return VoteResultInterface
     * @throws LocalizedException
     */
    public function dislike($articleId);
}
