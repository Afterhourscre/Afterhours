<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */


namespace Aheadworks\Faq\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\DB\Helper\Mysql\Fulltext;
use Aheadworks\Faq\Api\Data\ArticleInterface;

/**
 * Faq votes mysql resource
 */
class Search extends AbstractDb
{
    /**
     * @var Fulltext
     */
    private $fullText;
    
    /**
     * @param Fulltext $fulltext
     * @param Context $context
     * @param string $connectionName
     */
    public function __construct(
        Fulltext $fulltext,
        Context $context,
        $connectionName = null
    ) {
        parent::__construct($context, $connectionName);
        $this->fullText = $fulltext;
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init('aw_faq_article', 'article_id');
    }

    /**
     * Search Articles with query string
     * This method will return array of ids of found articles
     *
     * @param string $searchString
     * @param int $limit
     * @return array
     */
    public function searchQuery($searchString, $limit = null)
    {
        $matchQuery = $this->fullText->getMatchQuery(
            [ArticleInterface::TITLE, ArticleInterface::CONTENT],
            $searchString,
            Fulltext::FULLTEXT_MODE_BOOLEAN
        );
        
        $query = $this
            ->getConnection()
            ->select()
            ->from($this->getMainTable(), ArticleInterface::ARTICLE_ID)
            ->where($matchQuery)
            ->limit($limit);

        return $this->getConnection()->fetchAll($query);
    }
}
