<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Faq\Model;

use Magento\Framework\EntityManager\EntityManager;
use Magento\Framework\EntityManager\MetadataPool;
use \Magento\Framework\Api\AbstractSimpleObject;
use Aheadworks\Faq\Model\ResourceModel\VoteResult as VoteResource;
use \Aheadworks\Faq\Api\Data\VoteResultInterface;

/**
 * Class VoteResult
 * @package Aheadworks\Faq\Model
 */
class VoteResult extends AbstractSimpleObject implements VoteResultInterface
{
    /**
     * @param array $data
     */
    public function __construct(array $data = [])
    {
        parent::__construct($data);
    }

    /**
     * @inheritDoc
     */
    public function getLikeStatus()
    {
        return $this->_get(self::LIKE_STATUS);
    }

    /**
     * @inheritDoc
     */
    public function getDislikeStatus()
    {
        return $this->_get(self::DISLIKE_STATUS);
    }

    /**
     * @inheritDoc
     */
    public function setLikeStatus($status)
    {
        return $this->setData(self::LIKE_STATUS, $status);
    }

    /**
     * @inheritDoc
     */
    public function setDislikeStatus($status)
    {
        return $this->setData(self::DISLIKE_STATUS, $status);
    }

    /**
     * @inheritDoc
     */
    public function getHelpfulnessRating()
    {
        return $this->_get(self::HELPFULNESS_RATING);
    }

    /**
     * @inheritDoc
     */
    public function setHelpfulnessRating($rating)
    {
        return $this->setData(self::HELPFULNESS_RATING, $rating);
    }
}
