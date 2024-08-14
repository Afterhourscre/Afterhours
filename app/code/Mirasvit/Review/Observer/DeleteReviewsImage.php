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

namespace Mirasvit\Review\Observer;

use Magento\Framework\Event\ObserverInterface;
use Mirasvit\Review\Api\Data\ReviewInterface;
use Mirasvit\Review\Repository\ReviewRepository;
use Mirasvit\Review\Model\Uploader;

class DeleteReviewsImage implements ObserverInterface
{
    private $reviewRepository;

    private $uploader;

    public function __construct(
        ReviewRepository $reviewRepository,
        Uploader         $uploader
    ) {
        $this->uploader         = $uploader;
        $this->reviewRepository = $reviewRepository;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $_product = $observer->getEvent()->getProduct();

        $reviews = $this->reviewRepository->getCollection()->addFieldToFilter(ReviewInterface::PRODUCT_ID, $_product->getId());

        foreach ($reviews as $review) {
            $this->uploader->deleteImage((int)$review->getId());
        }

        return $this;
    }
}
