<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */


namespace Aheadworks\Faq\Api;

use Aheadworks\Faq\Api\Data\ArticleInterface;
use Aheadworks\Faq\Api\Data\ArticleSearchResultsInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\LocalizedException;

/**
 * FAQ article CRUD interface
 *
 * @api
 */
interface ArticleRepositoryInterface
{
    /**
     * Save article
     *
     * @param ArticleInterface $article
     * @return ArticleInterface
     * @throws LocalizedException
     */
    public function save(ArticleInterface $article);

    /**
     * Retrieve article
     *
     * @param int $articleId
     * @return ArticleInterface
     * @throws LocalizedException
     */
    public function getById($articleId);

    /**
     * Retrieve articles matching the specified criteria
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return ArticleSearchResultsInterface
     * @throws LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria);

    /**
     * Delete article
     *
     * @param ArticleInterface $article
     * @return bool true on success
     * @throws LocalizedException
     */
    public function delete(ArticleInterface $article);

    /**
     * Delete article by ID
     *
     * @param int $articleId
     * @return bool true on success
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function deleteById($articleId);
}
