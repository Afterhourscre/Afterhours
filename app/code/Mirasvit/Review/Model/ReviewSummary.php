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

namespace Mirasvit\Review\Model;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\DataObject\IdentityInterface;
use Mirasvit\Review\Api\Data\ReviewSummaryInterface;

class ReviewSummary extends AbstractModel implements IdentityInterface, ReviewSummaryInterface
{

    const CACHE_TAG = 'mst_review_summary';

    protected $_cacheTag    = 'mst_review_summary';

    protected $_eventPrefix = 'mst_review_summary';

    protected function _construct()
    {
        $this->_init('Mirasvit\Review\Model\ResourceModel\ReviewSummary');
    }

    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    public function getDetail(): string
    {
        return (string)$this->getData(self::DETAIL);
    }

    public function setDetail(string $value): ReviewSummaryInterface
    {
        return $this->setData(self::DETAIL, $value);
    }

    public function getProductId(): int
    {
        return (int)$this->getData(self::PRODUCT_ID);
    }

    public function setProductId(int $value): ReviewSummaryInterface
    {
        return $this->setData(self::PRODUCT_ID, $value);
    }

    public function getStoreId(): int
    {
        return (int)$this->getData(self::STORE_ID);
    }

    public function setStoreId(int $value): ReviewSummaryInterface
    {
        return $this->setData(self::STORE_ID, $value);
    }

    public function getGeneratedAt(): string
    {
        return $this->getData(self::GENERATED_AT);
    }

    public function setGeneratedAt(string $value): ReviewSummaryInterface
    {
        return $this->setData(self::GENERATED_AT, $value);
    }

    public function getPros(): string
    {
        return (string)$this->getData(self::PROS);
    }

    public function setPros(string $value): ReviewSummaryInterface
    {
        return $this->setData(self::PROS, $value);
    }

    public function getCons(): string
    {
        return (string)$this->getData(self::CONS);
    }

    public function setCons(string $value): ReviewSummaryInterface
    {
        return $this->setData(self::CONS, $value);
    }

    public function getProcessedReviews(): int
    {
        return (int)$this->getData(self::PROCESSED_REVIEWS);
    }

    public function setProcessedReviews(int $value): ReviewSummaryInterface
    {
        return $this->setData(self::PROCESSED_REVIEWS, $value);
    }

    public function getLastReviewId(): int
    {
        return (int)$this->getData(self::LAST_REVIEW_ID);
    }

    public function setLastReviewId(int $value): ReviewSummaryInterface
    {
        return $this->setData(self::LAST_REVIEW_ID, $value);
    }


}
