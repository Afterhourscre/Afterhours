<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */


namespace Aheadworks\Faq\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * FAQ category interface
 *
 * @api
 */
interface CategoryInterface extends ExtensibleDataInterface
{
    /**#@+
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const CATEGORY_ID              = 'category_id';
    const NAME                     = 'name';
    const IS_ENABLE                = 'is_enable';
    const URL_KEY                  = 'url_key';
    const STORE_IDS                = 'store_ids';
    const SORT_ORDER               = 'sort_order';
    const NUM_ARTICLES_TO_DISPLAY  = 'num_articles_to_display';
    const META_TITLE               = 'meta_title';
    const META_DESCRIPTION         = 'meta_description';
    const CATEGORY_ICON            = 'category_icon';
    const ARTICLE_LIST_ICON        = 'article_list_icon';
    const CREATED_AT               = 'created_at';
    const UPDATED_AT               = 'update_at';

    /**#@-*/

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getCategoryId();

    /**
     * Get name
     *
     * @return string
     */
    public function getName();

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
     * Get sort order
     *
     * @return integer|null
     */
    public function getSortOrder();

    /**
     * Get store view Ids
     *
     * @return array of int|null
     */
    public function getStoreIds();

    /**
     * Get number of articles to display at FAQ Home Page
     *
     * @return integer|null
     */
    public function getNumArticlesToDisplay();

    /**
     * Get name of category icon
     *
     * @return string|null
     */
    public function getCategoryIcon();

    /**
     * Get an icon name for article listing
     *
     * @return string|null
     */
    public function getArticleListIcon();

    /**
     * Is enable
     *
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getIsEnable();

    /**
     * Get creation time
     *
     * @return string
     */
    public function getCreatedAt();

    /**
     * Get creation time
     *
     * @return string
     */
    public function getUpdatedAt();

    /**
     * Set ID
     *
     * @param int $id
     * @return $this
     */
    public function setCategoryId($id);

    /**
     * Set name
     *
     * @param string $name
     * @return $this
     */
    public function setName($name);

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
     * Set sort order
     *
     * @param integer $sortOrder
     * @return $this
     */
    public function setSortOrder($sortOrder);

    /**
     * Set store view Ids
     *
     * @param array $storeIds
     * @return $this
     */
    public function setStoreIds($storeIds);

    /**
     * Set number of articles to display at FAQ Home Page
     *
     * @param integer $articlesNumber
     * @return $this
     */
    public function setNumArticlesToDisplay($articlesNumber);

    /**
     * Set name of category icon
     *
     * @param string $categoryIcon
     * @return $this
     */
    public function setCategoryIcon($categoryIcon);

    /**
     * Set an icon name for article listing
     *
     * @param string $articleListIcon
     * @return $this
     */
    public function setArticleListIcon($articleListIcon);

    /**
     * Set is enable
     *
     * @param bool $isEnable
     * @return $this
     */
    public function setIsEnable($isEnable);

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
}
