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

interface ReviewMediaInterface
{
    const TABLE_NAME = 'mst_review_media';

    const MEDIA_ID   = 'media_id';
    const REVIEW_ID  = 'review_id';
    const PRODUCT_ID = 'product_id';
    const TYPE       = 'type';
    const VALUE      = 'value';


    public function getReviewId(): int;

    public function setReviewId(int $value): self;

    public function getProductId(): int;

    public function setProductId(int $value): self;

    public function getType(): string;

    public function setType(string $value): self;

    public function getValue(): string;

    public function setValue(string $value): self;

}
