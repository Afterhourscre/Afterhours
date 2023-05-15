<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Faq\Api\Data;

use \Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface ArticleInterface
 * @package Aheadworks\Faq\Api\Data
 */
interface ArticleInterface extends ExtensibleDataInterface
{
    /**#@+
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const ARTICLE_ID               = 'article_id';
    const TITLE                    = 'title';
    const IS_ENABLE                = 'is_enable';
    const URL_KEY                  = 'url_key';
    const STORE_IDS                = 'store_ids';
    const SORT_ORDER               = 'sort_order';
    const CONTENT                  = 'content';
    const META_TITLE               = 'meta_title';
    const META_DESCRIPTION         = 'meta_description';
    const CREATED_AT               = 'created_at';
    const UPDATED_AT               = 'update_at';
    const VOTES_YES                = 'votes_yes';
    const VOTES_NO                 = 'votes_no';
    const VIEWS_COUNT              = 'views_count';
    const CATEGORY_ID              = 'category_id';
    const HELPFULNESS_RATING       = 'helpfulness_rating';
    /**#@-*/

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getArticleId();

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle();

    /**
     * Get URL-key
     *
     * @return string
     */
    public function getUrlKey();

    /**
     * Get meta title
     *
     * @return string|null
     */
    public function getMetaTitle();

    /**
     * Get meta description
     *
     * @return string|null
     */
    public function getMetaDescription();

    /**
     * Get creation time
     *
     * @return string
     */
    public function getCreatedAt();

    /**
     * Get update time
     *
     * @return string|null
     */
    public function getUpdatedAt();

    /**
     * Get sort order
     *
     * @return integer|null
     */
    public function getSortOrder();

    /**
     * Get store view Ids
     *
     * @return int[]|null
     */
    public function getStoreIds();

    /**
     * Get category Id
     *
     * @return int|null
     */
    public function getCategoryId();

    /**
     * Get content
     *
     * @return string|null
     */
    public function getContent();

    /**
     * Get number of positive votes
     *
     * @return integer|null
     */
    public function getVotesYes();

    /**
     * Get number of negative votes
     *
     * @return integer|null
     */
    public function getVotesNo();

    /**
     * Get number of views
     *
     * @return integer|null
     */
    public function getViewCount();

    /**
     * Is enable
     *
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getIsEnable();

    /**
     * Set ID
     *
     * @param int $id
     * @return $this
     */
    public function setArticleId($id);

    /**
     * Set title
     *
     * @param string $title
     * @return $this
     */
    public function setTitle($title);

    /**
     * Set URL-key
     *
     * @param string $urlKey
     * @return $this
     */
    public function setUrlKey($urlKey);

    /**
     * Set meta title
     *
     * @param string $metaTitle
     * @return $this
     */
    public function setMetaTitle($metaTitle);

    /**
     * Set meta description
     *
     * @param string $metaDescription
     * @return $this
     */
    public function setMetaDescription($metaDescription);

    /**
     * Set creation time
     *
     * @param string $creationTime
     * @return $this
     */
    public function setCreatedAt($creationTime);

    /**
     * Set update time
     *
     * @param string $updateTime
     * @return $this
     */
    public function setUpdatedAt($updateTime);

    /**
     * Set sort order
     *
     * @param integer $sortOrder
     * @return $this
     */
    public function setSortOrder($sortOrder);

    /**
     * Set store view Ids
     *
     * @param int[] $storeIds
     * @return $this
     */
    public function setStoreIds($storeIds);

    /**
     * Set category Id
     *
     * @param int $categoryId
     * @return $this
     */
    public function setCategoryId($categoryId);

    /**
     * Set content
     *
     * @param string $content
     * @return $this
     */
    public function setContent($content);

    /**
     * Set number of positive votes
     *
     * @param integer $votesYes
     * @return $this
     */
    public function setVotesYes($votesYes);

    /**
     * Set number of negative votes
     *
     * @param integer $votesNo
     * @return $this
     */
    public function setVotesNo($votesNo);

    /**
     * Set number of views
     *
     * @param integer $viewCount
     * @return $this
     */
    public function setViewsCount($viewCount);

    /**
     * Set is enable
     *
     * @param bool $isEnable
     * @return $this
     */
    public function setIsEnable($isEnable);

    /**
     * Get helpfulness rating
     *
     * @return float
     */
    public function getHelpfulnessRating();

    /**
     * Set helpfulness rating
     *
     * @param float $rating
     * @return $this
     */
    public function setHelpfulnessRating($rating);
}
