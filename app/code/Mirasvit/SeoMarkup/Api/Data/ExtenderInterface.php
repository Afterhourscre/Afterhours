<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-seo
 * @version   2.9.6
 * @copyright Copyright (C) 2024 Mirasvit (https://mirasvit.com/)
 */


declare(strict_types=1);

namespace Mirasvit\SeoMarkup\Api\Data;

interface ExtenderInterface
{
    public const REQUEST_PARAM_ID = 'id';

    public const EXTENDER_ID           = 'extender_id';
    public const NAME                  = 'name';
    public const IS_ACTIVE             = 'is_active';
    public const ENTITY_TYPE_ID        = 'entity_type_id';
    public const STORE_IDS             = 'store_ids';
    public const SNIPPET               = 'snippet';
    public const OVERRIDE              = 'override';
    public const CONDITIONS_SERIALIZED = 'conditions_serialized';

    public const SNIPPET_ARRAY = 'snippet_array';

    public const RULE       = 'rule';
    public const CONDITIONS = 'conditions';

    public const PRODUCT_TYPE = 'product';
    public const OFFER_TYPE   = 'offer';

    public const ATTRIBUTES
        = [
            self::EXTENDER_ID,
            self::IS_ACTIVE,
            self::ENTITY_TYPE_ID,
            self::STORE_IDS,
            self::SNIPPET,
            self::OVERRIDE,
            self::CONDITIONS_SERIALIZED,
        ];

    public const ENTITY_TYPES
        = [
            self::PRODUCT_TYPE,
            self::OFFER_TYPE,
        ];

    public const DATA_PERSISTOR_KEY = 'rich_snippet_extender';

    public function getExtenderId(): ?int;

    public function setExtenderId(int $extenderId): self;

    public function getName(): string;

    public function setName(string $name): self;

    public function isActive(): bool;

    public function setIsActive(bool $isActive): self;

    public function getEntityTypeId(): string;

    public function setEntityTypeId(string $entityTypeId): self;

    public function getStoreIds(): array;

    public function setStoreIds(array $storeIds): self;

    public function getSnippet(): string;

    public function setSnippet(string $snippet): self;

    public function getSnippetArray(): array;

    public function setSnippetArray(array $snippet): self;

    public function isOverrideEnabled(): bool;

    public function setIsOverrideEnabled(bool $override): self;

    public function getConditionsSerialized(): ?string;

    public function setConditionsSerialized(string $conditions): self;
}
