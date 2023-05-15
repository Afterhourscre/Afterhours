<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Faq\Block\Search;

use Aheadworks\Faq\Model\Url;
use Aheadworks\Faq\Block\AbstractTemplate;

/**
 * FAQ search report page content block
 *
 * Class Search
 * @package Aheadworks\Faq\Block\Search
 */
class Search extends AbstractTemplate
{
    /**
     * Retrieve search query string
     *
     * @return string
     */
    public function getSearchQueryString()
    {
        return $this->getRequest()->getParam($this->getQueryParamName());
    }

    /**
     * Retrieve FAQ Search query parameter name
     *
     * @return string
     */
    public function getQueryParamName()
    {
        return Url::FAQ_QUERY_PARAM;
    }

    /**
     * Retrieve route name where request came from
     *
     * @return string
     */
    public function getRouteName()
    {
        return substr($this->getRequest()->getPathInfo(), 1);
    }
}
