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


interface ReviewSummaryInterface
{
    const TABLE_NAME = 'mst_review_summary';

    const ID                = 'summary_id';
    const PRODUCT_ID        = 'product_id';
    const STORE_ID          = 'store_id';
    const DETAIL            = 'detail';
    const PROS              = 'pros';
    const GENERATED_AT      = 'generated_at';
    const CONS              = 'cons';
    const LAST_REVIEW_ID    = 'last_review_id';
    const PROCESSED_REVIEWS = 'processed_reviews';


    public function getDetail(): string;

    public function setDetail(string $value): self;

    public function getProductId(): int;

    public function setProductId(int $value): self;

    public function getStoreId(): int;

    public function setStoreId(int $value): self;

    public function getGeneratedAt(): string;

    public function setGeneratedAt(string $value): self;

    public function getPros(): string;

    public function setPros(string $value): self;

    public function getCons(): string;

    public function setCons(string $value): self;

    public function getProcessedReviews(): int;

    public function setProcessedReviews(int $value): self;

    public function getLastReviewId(): int;

    public function setLastReviewId(int $value): self;


}
