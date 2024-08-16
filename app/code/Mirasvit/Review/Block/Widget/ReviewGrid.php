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


namespace Mirasvit\Review\Block\Widget;


use Mirasvit\Review\Api\Data\ReviewInterface;
use Mirasvit\Review\Block\Widget\Review\Card;
use Mirasvit\Review\Repository\ReviewRepository;
use Mirasvit\Review\Model\ResourceModel\Review\Collection as ReviewCollection;
use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;

class ReviewGrid extends Template implements BlockInterface
{
    protected $_template = 'Mirasvit_Review::widget/masonry.phtml';

    protected $reviewRepository;

    public function __construct(
        ReviewRepository $reviewRepository,
        Template\Context $context,
        array $data = []
    ) {
        $this->reviewRepository = $reviewRepository;

        parent::__construct($context, $data);
    }

    /**
     * @return ReviewInterface[]|null
     */
    public function getReviews(): ?array
    {
        $collection = $this->reviewRepository->getCollection();

        $this->setSortOrder($collection);
        $this->setLimit($collection);

        return $collection->getItems();
    }

    private function setSortOrder(ReviewCollection $collection)
    {
        $sortOrder = (string)$this->getData('sort_order');

        [$field, $dir] = explode('-', $sortOrder);

        if (!$field) {
            $field = 'created_at';
        }

        $collection->setSortOrder($field, $dir);
    }

    private function setLimit(ReviewCollection $collection)
    {
        $limit = $this->getPageSize();

        $collection->setPageSize($limit);
    }

    public function getReviewBlock(): Card
    {
        $block = $this->getLayout()->createBlock(Card::class);

        return $block;
    }

    public function getIdentifier(): string
    {
        return spl_object_hash($this);
    }

    public function getTotalSize(): int
    {
        return (int)$this->reviewRepository->getCollection()->getSize();
    }

    public function getPageSize(): int
    {
        $limit = $this->getData('page_size') ?: 1000;

        if ($limit > 200) {
            $limit = 200;
        }

        if ($limit < 0) {
            $limit = 10;
        }

        return (int)$limit;
    }

    public function getRequestUrl(): string
    {
        return $this->getUrl('mst_review/review/ajax');
    }
}
