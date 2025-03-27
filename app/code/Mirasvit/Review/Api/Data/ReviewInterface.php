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
 * @package   mirasvit/module-review
 * @version   1.1.2
 * @copyright Copyright (C) 2024 Mirasvit (https://mirasvit.com/)
 */


declare(strict_types=1);


namespace Mirasvit\Review\Api\Data;


interface ReviewInterface
{
    const MAIN_TABLE              = 'review';
    const DETAIL_TABLE            = 'review_detail';
    const ENTITY_TABLE            = 'review_entity';
    const SUMMARY_TABLE           = 'review_entity_summary';
    const STORE_TABLE             = 'review_store';
    const STATUS_TABLE            = 'review_status';
    const DETAIL_ADDITIONAL_TABLE = 'mst_review_details_additional';
    const ATTACHMENT_TABLE        = 'mst_review_attachment';

    const ID                = 'review_id';
    const PRODUCT_ID        = 'entity_pk_value'; // review table
    const ENTITY_ID         = 'entity_id';
    const STATUS_ID         = 'status_id';
    const STATUS            = 'status';
    const STORE_ID          = 'store_id';
    const STORES            = 'stores';
    const TITLE             = 'title';
    const DETAIL            = 'detail';
    const NICKNAME          = 'nickname';
    const CUSTOMER_ID       = 'customer_id';
    const PROS              = 'pros';
    const IS_VERIFIED_BUYER = 'is_verified_buyer';
    const REINDEXED_AT      = 'reindexed_at';
    const CONS              = 'cons';
    const IP                = 'ip';
    const COUNTRY           = 'country';
    const COUNTRY_ISO       = 'country_iso';
    const LOCATION          = 'location';
    const PRODUCT_IDS       = 'product_ids';
    const PRODUCT_INFO      = 'product_info';
    const CREATED_AT        = 'created_at';

    const PRODUCT_ENTITY_ID   = 1;
    const STATUS_APPROVED     = 1;
    const STATUS_PENDING      = 2;
    const STATUS_NOT_APPROVED = 3;

    public function getTitle(): string;

    public function setTitle(string $value): self;

    public function getDetail(): string;

    public function setDetail(string $value): self;

    public function getProductId(): int;

    public function setProductId(int $value): self;

    public function getStoreId(): int;

    public function setStoreId(int $value): self;

    public function getCustomerId(): ?int;

    public function setCustomerId(?int $value): self;

    public function getNickname(): string;

    public function setNickname(string $value): self;

    public function getCreatedAt(): string;

    public function setCreatedAt(string $value): self;

    public function getPros(): string;

    public function setPros(string $value): self;

    public function getCons(): string;

    public function setCons(string $value): self;

    public function getIsVerifiedBuyer(): bool;

    public function setIsVerifiedBuyer(bool $value): self;

    public function getReindexedAt(): string;

    public function setReindexedAt(string $value): self;

    public function getIp(): string;

    public function setIp(string $value): self;

    public function getCountry(): string;

    public function setCountry(string $value): self;

    public function getCountryIso(): string;

    public function setCountryIso(string $value): self;

    public function getLocation(): string;

    public function setLocation(string $value): self;

    public function getProductIds(): array;

    public function setProductIds(array $value): self;

    public function getProductInfo(): ?array;

    public function setProductInfo(array $value): self;

}
