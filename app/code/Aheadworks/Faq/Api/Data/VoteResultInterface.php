<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Faq\Api\Data;

/**
 * Interface VoteResultInterface
 * @package Aheadworks\Faq\Api\Data
 */
interface VoteResultInterface
{
    /**#@+
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const LIKE_STATUS = 'like';
    const DISLIKE_STATUS = 'dislike';
    const HELPFULNESS_RATING = 'helpfulness_rating';
    /**#@-*/

    /**
     * Get like status
     *
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getLikeStatus();

    /**
     * Get dislike status
     *
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getDislikeStatus();

    /**
     * Set like status
     *
     * @param bool $status
     * @return $this
     */
    public function setLikeStatus($status);

    /**
     * Set dislike status
     *
     * @param bool $status
     * @return $this
     */
    public function setDislikeStatus($status);

    /**
     * Get rating message
     *
     * @return string
     */
    public function getHelpfulnessRating();

    /**
     * @param string $message
     * @return $this
     */
    public function setHelpfulnessRating($message);
}
