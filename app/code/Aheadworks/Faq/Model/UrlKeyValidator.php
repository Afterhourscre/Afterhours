<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */


namespace Aheadworks\Faq\Model;

use Magento\Framework\Validator\AbstractValidator;
use Aheadworks\Faq\Model\Article;
use Aheadworks\Faq\Model\Category;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\EntityManager\TypeResolver;
use Magento\Framework\Api\SimpleDataObjectConverter;

/**
 * FAQ Url key validator
 */
class UrlKeyValidator extends AbstractValidator
{
    /**
     * @var MetadataPool
     */
    private $metadataPool;

    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var TypeResolver
     */
    private $typeResolver;

    /**
     * @param MetadataPool $metadataPool
     * @param ResourceConnection $resourceConnection
     * @param TypeResolver $typeResolver
     */
    public function __construct(
        MetadataPool $metadataPool,
        ResourceConnection $resourceConnection,
        TypeResolver $typeResolver
    ) {
        $this->metadataPool = $metadataPool;
        $this->resourceConnection = $resourceConnection;
        $this->typeResolver = $typeResolver;
    }

    /**
     * Validate url key
     *
     * @param Article|Category $model
     * @return bool
     */
    public function isValid($model)
    {
        $errors = [];
        if (!$this->isValidUrlKey($model)) {
            $errors[] = __('The URL key contains capital letters or disallowed symbols.');
        }
        if ($this->isNumericUrlKey($model)) {
            $errors[] = __('The URL key cannot be made of only numbers.');
        }
        if (!$this->isUniqueUrlKey($model)) {
            $errors[] = __('The URL key already exist.');
        }

        $this->_addMessages($errors);

        return empty($errors);
    }

    /**
     * Return message about validation
     *
     * @return string
     */
    public function getMessage()
    {
        return 'This URL key is invalid.';
    }

    /**
     * Check if url key is unique
     *
     * @param Article|Category $model
     * @return bool
     */
    private function isUniqueUrlKey($model)
    {
        $urlKey = $model->getUrlKey();
        $entityType = $this->typeResolver->resolve($model);
        $modelMetadata = $this->metadataPool->getMetadata($entityType);
        $connection = $this->resourceConnection->getConnectionByName(
            $modelMetadata->getEntityConnectionName()
        );
        $mainTable = $this->resourceConnection->getTableName($modelMetadata->getEntityTable());
        $camelCaseIdentifierField = SimpleDataObjectConverter::snakeCaseToUpperCamelCase(
            $modelMetadata->getIdentifierField()
        );
        $idGetter = 'get' . $camelCaseIdentifierField;
        $id = $model->$idGetter();

        $tables = [
            $this->resourceConnection->getTableName('aw_faq_article'),
            $this->resourceConnection->getTableName('aw_faq_category')
        ];

        foreach ($tables as $table) {
            $select = $connection->select()
                ->from(['faq' => $table], 'url_key')
                ->where('faq.url_key = ?', $urlKey);
            if ($table == $mainTable && $id) {
                $select->where('faq.' . $modelMetadata->getIdentifierField() . ' != ?', $id);
            }

            $result = $connection->fetchAll($select);

            if (count($result) != 0) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check whether url key is numeric
     *
     * @param Article|Category $model
     * @return bool
     */
    private function isNumericUrlKey($model)
    {
        return preg_match('/^[0-9]+$/', $model->getUrlKey());
    }

    /**
     * Check whether url key is valid
     *
     * @param Article|Category $model
     * @return bool
     */
    private function isValidUrlKey($model)
    {
        return preg_match('/^[a-z0-9][a-z0-9_\/-]+(\.[a-z0-9_-]+)?$/', $model->getUrlKey());
    }
}
