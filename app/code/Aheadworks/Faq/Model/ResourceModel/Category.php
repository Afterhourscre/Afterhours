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
use Aheadworks\Faq\Model\Category\Validator;
use Aheadworks\Faq\Api\Data\CategoryInterface;
use Aheadworks\Faq\Api\Data\CategoryInterfaceFactory as CategoryFactory;

/**
 * Faq category mysql resource
 */
class Category extends AbstractDb
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var MetadataPool
     */
    protected $metadataPool;

    /**
     * @var Validator
     */
    private $validator;

    /**
     * @var CategoryFactory
     */
    private $categoryFactory;

    /**
     * @param Context $context
     * @param MetadataPool $metadataPool
     * @param EntityManager $entityManager
     * @param Validator $validator
     * @param CategoryFactory $categoryFactory
     * @param string $connectionName
     */
    public function __construct(
        Context $context,
        MetadataPool $metadataPool,
        EntityManager $entityManager,
        Validator $validator,
        CategoryFactory $categoryFactory,
        $connectionName = null
    ) {
        parent::__construct($context, $connectionName);
        $this->validator = $validator;
        $this->entityManager = $entityManager;
        $this->metadataPool = $metadataPool;
        $this->categoryFactory = $categoryFactory;
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('aw_faq_category', 'category_id');
    }

    /**
     * Return Id of Category by Url-key
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
     * @param int $categoryId
     * @param string|null $field
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function load(AbstractModel $object, $categoryId, $field = null)
    {
        $this->entityManager->load($object, $categoryId);
        return $this;
    }

    /**
     * @return Validator
     */
    public function getValidationRulesBeforeSave()
    {
        return $this->validator;
    }
}
