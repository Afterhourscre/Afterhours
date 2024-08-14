<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */


namespace Aheadworks\Faq\Model;

use Aheadworks\Faq\Api\Data\CategoryInterface;
use Aheadworks\Faq\Model\ResourceModel\Category as CategoryResource;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;

/**
 * Category model
 */
class Category extends AbstractModel implements CategoryInterface, IdentityInterface
{
    /**
     * Category page cache tag
     */
    const CACHE_TAG = 'faq_category';
    
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(CategoryResource::class);
    }

    /**
     * Return unique ID(s) for each object in system
     *
     * @return array
     */
    public function getCategoryId()
    {
        return $this->getData(self::CATEGORY_ID);
    }

    /**
     * Return unique ID(s) for each object in system
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getCategoryId()];
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->getData(self::NAME);
    }

    /**
     * Get URL-key
     *
     * @return string
     */
    public function getUrlKey()
    {
        return $this->getData(self::URL_KEY);
    }

    /**
     * Get meta title
     *
     * @return string|null
     */
    public function getMetaTitle()
    {
        return $this->getData(self::META_TITLE);
    }

    /**
     * Get meta description
     *
     * @return string|null
     */
    public function getMetaDescription()
    {
        return $this->getData(self::META_DESCRIPTION);
    }

    /**
     * Get sort order
     *
     * @return integer|null
     */
    public function getSortOrder()
    {
        return $this->getData(self::SORT_ORDER);
    }

    /**
     * Get store view
     *
     * @return array of int|null
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
     * Get number of articles to display at FAQ Home Page
     *
     * @return integer|null
     */
    public function getNumArticlesToDisplay()
    {
        return $this->getData(self::NUM_ARTICLES_TO_DISPLAY);
    }

    /**
     * Get name of category icon
     *
     * @return string|null
     */
    public function getCategoryIcon()
    {
        return $this->getData(self::CATEGORY_ICON);
    }

    /**
     * Get an icon name for article listing
     *
     * @return string|null
     */
    public function getArticleListIcon()
    {
        return $this->getData(self::ARTICLE_LIST_ICON);
    }

    /**
     * Is enable
     *
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getIsEnable()
    {
        return (bool) $this->getData(self::IS_ENABLE);
    }

    /**
     * Get creation time
     *
     * @return string
     */
    public function getCreatedAt()
    {
        return $this->getData(self::CREATED_AT);
    }

    /**
     * Get creation time
     *
     * @return string
     */
    public function getUpdatedAt()
    {
        return $this->getData(self::UPDATED_AT);
    }

    /**
     * Set ID
     *
     * @param int $id
     * @return CategoryInterface
     */
    public function setCategoryId($id)
    {
        return $this->setData(self::CATEGORY_ID, $id);
    }

    /**
     * Set name
     *
     * @param string $name
     * @return CategoryInterface
     */
    public function setName($name)
    {
        return $this->setData(self::NAME, $name);
    }

    /**
     * Set URL-key
     *
     * @param string $urlKey
     * @return CategoryInterface
     */
    public function setUrlKey($urlKey)
    {
        return $this->setData(self::URL_KEY, $urlKey);
    }

    /**
     * Set meta title
     *
     * @param string $metaTitle
     * @return CategoryInterface
     */
    public function setMetaTitle($metaTitle)
    {
        return $this->setData(self::META_TITLE, $metaTitle);
    }

    /**
     * Set meta description
     *
     * @param string $metaDescription
     * @return CategoryInterface
     */
    public function setMetaDescription($metaDescription)
    {
        return $this->setData(self::META_DESCRIPTION, $metaDescription);
    }

    /**
     * Set sort order
     *
     * @param integer $sortOrder
     * @return CategoryInterface
     */
    public function setSortOrder($sortOrder)
    {
        return $this->setData(self::SORT_ORDER, $sortOrder);
    }

    /**
     * Set store view
     *
     * @param string $storeIds
     * @return CategoryInterface
     */
    public function setStoreIds($storeIds)
    {
        return $this->setData(self::STORE_IDS, $storeIds);
    }

    /**
     * Set number of articles to display at FAQ Home Page
     *
     * @param integer $articlesNumber
     * @return CategoryInterface
     */
    public function setNumArticlesToDisplay($articlesNumber)
    {
        return $this->setData(self::NUM_ARTICLES_TO_DISPLAY, $articlesNumber);
    }

    /**
     * Set name of category icon
     *
     * @param string $categoryIcon
     * @return CategoryInterface
     */
    public function setCategoryIcon($categoryIcon)
    {
        return $this->setData(self::CATEGORY_ICON, $categoryIcon);
    }

    /**
     * Set an icon name for article listing
     *
     * @param string $articleListIcon
     * @return CategoryInterface
     */
    public function setArticleListIcon($articleListIcon)
    {
        return $this->setData(self::ARTICLE_LIST_ICON, $articleListIcon);
    }

    /**
     * Set is enable
     *
     * @param bool $isEnable
     * @return CategoryInterface
     */
    public function setIsEnable($isEnable)
    {
        return $this->setData(self::IS_ENABLE, $isEnable);
    }

    /**
     * Set creation time
     *
     * @param string $creationTime
     * @return CategoryInterface
     */
    public function setCreatedAt($creationTime)
    {
        return $this->setData(self::CREATED_AT, $creationTime);
    }

    /**
     * Set update time
     *
     * @param string $updateTime
     * @return CategoryInterface
     */
    public function setUpdatedAt($updateTime)
    {
        return $this->setData(self::UPDATED_AT, $updateTime);
    }
}
