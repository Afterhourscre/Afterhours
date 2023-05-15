<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Faq\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\EntityManager\EntityManager;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\EntityManager\MetadataPool;
use Aheadworks\Faq\Model\Article\Validator;
use Aheadworks\Faq\Api\Data\ArticleInterface;
use Aheadworks\Faq\Api\Data\ArticleInterfaceFactory as ArticleFactory;
use Aheadworks\Faq\Model\Calculator\Helpfulness;

/**
 * Class Article
 * @package Aheadworks\Faq\Model\ResourceModel
 */
class Article extends AbstractDb
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var MetadataPool
     */
    private $metadataPool;

    /**
     * @var Validator
     */
    private $validator;

    /**
     * @var Helpfulness
     */
    private $helpfulnessCalculator;

    /**
     * @param Context $context
     * @param MetadataPool $metadataPool
     * @param EntityManager $entityManager
     * @param Validator $validator
     * @param Calculator $calculator
     * @param null $connectionName
     */
    public function __construct(
        Context $context,
        MetadataPool $metadataPool,
        EntityManager $entityManager,
        Validator $validator,
        Helpfulness $helpfulnessCalculator,
        $connectionName = null
    ) {
        parent::__construct($context, $connectionName);
        $this->validator = $validator;
        $this->entityManager = $entityManager;
        $this->metadataPool = $metadataPool;
        $this->helpfulnessCalculator = $helpfulnessCalculator;
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init('aw_faq_article', 'article_id');
    }

    /**
     * Return Id of Article by Url-key
     *
     * @param string $urlKey
     * @return int|null
     */

    public function getIdByUrlKey($urlKey)
    {
        $select = $this->getConnection()->select()
            ->from(['cp' => $this->getMainTable()])
            ->where('cp.url_key = ?', $urlKey)
            ->limit(1);
        return $this->getConnection()->fetchOne($select);
    }

    /**
     * @inheritDoc
     */
    public function save(AbstractModel $object)
    {
        $this->entityManager->save($object);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function delete(AbstractModel $object)
    {
        $this->entityManager->delete($object);
        return $this;
    }

    /**
     * Load an object
     *
     * @param AbstractModel $object
     * @param int $articleId
     * @param string|null $field
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function load(AbstractModel $object, $articleId, $field = null)
    {
        $this->entityManager->load($object, $articleId);

        return $this;
    }

    /**
     * @return Validator
     */
    public function getValidationRulesBeforeSave()
    {
        return $this->validator;
    }

    /**
     * {@inheritdoc}
     */
    protected function _beforeSave(AbstractModel $article)
    {
        $helpfulnessRating = $this->helpfulnessCalculator->calculateHelpfulnessRating($article);
        $article->setHelpfulnessRating($helpfulnessRating);

        return parent::_beforeSave($article);
    }
}
