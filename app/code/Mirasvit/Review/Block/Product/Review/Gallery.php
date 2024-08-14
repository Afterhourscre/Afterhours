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


namespace Mirasvit\Review\Block\Product\Review;


use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\View\Element\Template;
use Mirasvit\Review\Api\Data\ReviewInterface;
use Mirasvit\Review\Api\Data\ReviewMediaInterface;
use Mirasvit\Review\Model\ResourceModel\ReviewMedia\Collection as MediaCollection;
use Mirasvit\Review\Repository\ReviewMediaRepository;

class Gallery extends Template
{
    protected $_template = 'Mirasvit_Review::review/gallery.phtml';

    protected $mediaRepository;

    protected $widgetBlock;

    protected $context;

    /** @var ReviewInterface|null */
    protected $review;

    /** @var int|null */
    protected $productId;

    public function __construct(
        ReviewMediaRepository $mediaRepository,
        Gallery\Widget        $widgetBlock,
        Template\Context      $context,
        array                 $data = []
    ) {
        $this->mediaRepository = $mediaRepository;
        $this->widgetBlock     = $widgetBlock;
        $this->context         = $context;

        parent::__construct($context, $data);
    }

    public function setProductId(int $productId): self
    {
        $this->productId = $productId;

        return $this;
    }

    public function setReview(ReviewInterface $review): self
    {
        $this->review = $review;

        return $this;
    }

    /**
     * @return ReviewMediaInterface[]|null
     */
    public function getImages(): ?array
    {
        if (!$this->productId && !$this->review) {
            return null;
        }

        $collection = $this->mediaRepository->getCollection()
            ->addFieldToFilter(ReviewMediaInterface::TYPE, 'image');

        $reviewTable = $collection->getTable(ReviewInterface::MAIN_TABLE);
        $collection
            ->getSelect()
            ->joinLeft(
                ['review' => $reviewTable],
                'main_table.' . ReviewMediaInterface::REVIEW_ID . ' = review.' . ReviewInterface::ID ,
                ReviewInterface::STATUS_ID
            )
            ->joinLeft(
                ['detail' => $collection->getTable(ReviewInterface::DETAIL_TABLE)],
                'main_table.' . ReviewInterface::ID . ' = detail.' . ReviewInterface::ID,
                ['store_id' => 'detail.store_id']
            );

        $collection->addFieldToFilter( ReviewInterface::STATUS_ID, ReviewInterface::STATUS_APPROVED)
            ->addFieldToFilter('store_id', ['in' => [0, $this->context->getStoreManager()->getStore()->getId()]]);

        if ($this->productId) {
            $collection->addFieldToFilter('main_table.' . ReviewMediaInterface::PRODUCT_ID, $this->productId);
        }

        if ($this->review) {
            $collection->addFieldToFilter('main_table.' . ReviewMediaInterface::REVIEW_ID, $this->review->getId());
        }

        $collection->setOrder('main_table.' . ReviewMediaInterface::REVIEW_ID, 'DESC')
            ->setOrder('main_table.' . ReviewMediaInterface::MEDIA_ID, 'ASC');

        return $collection->getItems();
    }

    public function getImageUrl(ReviewMediaInterface $image): string
    {
        return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA)
            . 'mst_review/'
            . $image->getReviewId() . '/'
            . $image->getValue();
    }

    public function getIdentifier(): string
    {
        $identifier = 'gallery';

        if ($this->productId) {
            $identifier .= '-' . $this->productId;
        }

        if ($this->review) {
            $identifier .= '-' . $this->review->getId();
        }

        return $identifier;
    }

    public function getWidgetTemplate(): string
    {
        return $this->widgetBlock->toHtml();
    }

    public function getReviewRequestUrl(): string
    {
        return $this->_urlBuilder->getUrl('mst_review/review/content');
    }
}
