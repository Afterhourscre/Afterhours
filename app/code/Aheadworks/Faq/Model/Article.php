<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Faq\Model;

use Aheadworks\Faq\Api\Data\ArticleInterface;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;
use Aheadworks\Faq\Model\ResourceModel\Article as ArticleResource;

/**
 * Class Article
 * @package Aheadworks\Faq\Model
 */
class Article extends AbstractModel implements ArticleInterface, IdentityInterface
{
    /**
     * FAQ article cache tag
     */
    const CACHE_TAG = 'faq_article';

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(ArticleResource::class);
    }

    /**
     * {@inheritdoc}
     */
    public function getArticleId()
    {
        return $this->getData(self::ARTICLE_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getArticleId()];
    }

    /**
     * {@inheritdoc}
     */
    public function getCategoryId()
    {
        return $this->getData(self::CATEGORY_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function getViewCount()
    {
        return $this->getData(self::VIEWS_COUNT);
    }

    /**
     * {@inheritdoc}
     */
    public function getStoreIds()
    {
        $ids = $this->getData(self::STORE_IDS);

        if (empty($ids)) {
            return null;
        }

        return array_map('intval', (array)$ids);
    }

    /**
     * {@inheritdoc}
     */
    public function getIsEnable()
    {
        return (bool)$this->getData(self::IS_ENABLE);
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
    public function getUpdatedAt()
    {
        return $this->getData(self::UPDATED_AT);
    }

    /**
     * {@inheritdoc}
     */
    public function getTitle()
    {
        return $this->getData(self::TITLE);
    }

    /**
     * {@inheritdoc}
     */
    public function getUrlKey()
    {
        return $this->getData(self::URL_KEY);
    }

    /**
     * {@inheritdoc}
     */
    public function getContent()
    {
        return $this->getData(self::CONTENT);
    }

    /**
     * {@inheritdoc}
     */
    public function getVotesYes()
    {
        return $this->getData(self::VOTES_YES);
    }

    /**
     * {@inheritdoc}
     */
    public function getVotesNo()
    {
        return $this->getData(self::VOTES_NO);
    }

    /**
     * {@inheritdoc}
     */
    public function getMetaTitle()
    {
        return $this->getData(self::META_TITLE);
    }

    /**
     * {@inheritdoc}
     */
    public function getMetaDescription()
    {
        return $this->getData(self::META_DESCRIPTION);
    }

    /**
     * {@inheritdoc}
     */
    public function getSortOrder()
    {
        return $this->getData(self::SORT_ORDER);
    }

    /**
     * {@inheritdoc}
     */
    public function setArticleId($id)
    {
        return $this->setData(self::ARTICLE_ID, $id);
    }

    /**
     * {@inheritdoc}
     */
    public function setTitle($title)
    {
        return $this->setData(self::TITLE, $title);
    }

    /**
     * {@inheritdoc}
     */
    public function setUrlKey($urlKey)
    {
        return $this->setData(self::URL_KEY, $urlKey);
    }

    /**
     * {@inheritdoc}
     */
    public function setMetaTitle($metaTitle)
    {
        return $this->setData(self::META_TITLE, $metaTitle);
    }

    /**
     * {@inheritdoc}
     */
    public function setMetaDescription($metaDescription)
    {
        return $this->setData(self::META_DESCRIPTION, $metaDescription);
    }

    /**
     * {@inheritdoc}
     */
    public function setSortOrder($sortOrder)
    {
        return $this->setData(self::SORT_ORDER, $sortOrder);
    }

    /**
     * {@inheritdoc}
     */
    public function setContent($content)
    {
        return $this->setData(self::CONTENT, $content);
    }

    /**
     * {@inheritdoc}
     */
    public function setVotesYes($votesYes)
    {
        return $this->setData(self::VOTES_YES, $votesYes);
    }

    /**
     * {@inheritdoc}
     */
    public function setVotesNo($votesNo)
    {
        return $this->setData(self::VOTES_NO, $votesNo);
    }

    /**
     * {@inheritdoc}
     */
    public function getTotalVotes()
    {
        return $this->getVotesNo() + $this->getVotesYes();
    }

    /**
     * {@inheritdoc}
     */
    public function setIsEnable($isEnable)
    {
        return $this->setData(self::IS_ENABLE, $isEnable);
    }

    /**
     * {@inheritdoc}
     */
    public function setCreatedAt($creationTime)
    {
        return $this->setData(self::CREATED_AT, $creationTime);
    }

    /**
     * {@inheritdoc}
     */
    public function setUpdatedAt($updateTime)
    {
        return $this->setData(self::UPDATED_AT, $updateTime);
    }

    /**
     * {@inheritdoc}
     */
    public function setStoreIds($storeIds)
    {
        return $this->setData(self::STORE_IDS, $storeIds);
    }

    /**
     * {@inheritdoc}
     */
    public function setViewsCount($viewCount)
    {
        return $this->setData(self::VIEWS_COUNT, $viewCount);
    }

    /**
     * {@inheritdoc}
     */
    public function setCategoryId($categoryId)
    {
        return $this->setData(self::CATEGORY_ID, $categoryId);
    }

    /**
     * {@inheritdoc}
     */
    public function getHelpfulnessRating()
    {
        return $this->getData(self::HELPFULNESS_RATING);
    }

    /**
     * {@inheritdoc}
     */
    public function setHelpfulnessRating($rating)
    {
        $this->setData(self::HELPFULNESS_RATING, $rating);
    }
}
