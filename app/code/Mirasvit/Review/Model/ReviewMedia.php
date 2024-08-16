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

use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;
use Mirasvit\Review\Api\Data\ReviewMediaInterface;

class ReviewMedia extends AbstractModel implements IdentityInterface, ReviewMediaInterface
{
    const CACHE_TAG = 'mst_review_media';

    protected $_cacheTag    = 'mst_review_media';

    protected $_eventPrefix = 'mst_review_media';

    protected function _construct()
    {
        $this->_init('Mirasvit\Review\Model\ResourceModel\ReviewMedia');
    }

    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    public function getReviewId(): int
    {
        return (int)$this->getData(self::REVIEW_ID);
    }

    public function setReviewId(int $value): self
    {
        return $this->setData(self::REVIEW_ID, $value);
    }

    public function getProductId(): int
    {
        return (int)$this->getData(self::PRODUCT_ID);
    }

    public function setProductId(int $value): self
    {
        return $this->setData(self::PRODUCT_ID, $value);
    }

    public function getType(): string
    {
        return (string)$this->getData(self::TYPE);
    }

    public function setType(string $value): self
    {
        return $this->setData(self::TYPE, $value);
    }

    public function getValue(): string
    {
        return (string)$this->getData(self::VALUE);
    }

    public function setValue(string $value): self
    {
        return $this->setData(self::VALUE, $value);
    }

}
