<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */


namespace Aheadworks\Faq\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Aheadworks\Faq\Api\Data\ArticleSearchResultsInterface;

/**
 * FAQ search interface
 *
 * @api
 */
interface SearchManagementInterface
{
    /**
     * Make Full Text Search and return found Articles
     *
     * @param string $searchString
     * @param int $storeId
     * @param int $limit
     * @return ArticleSearchResultsInterface
     * @internal param SearchCriteriaInterface $searchCriteria
     */
    public function searchArticles($searchString, $storeId, $limit = null);
}
