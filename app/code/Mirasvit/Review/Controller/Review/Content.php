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


namespace Mirasvit\Review\Controller\Review;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\LayoutInterface;
use Mirasvit\Core\Service\SerializeService;
use Mirasvit\Review\Api\Data\ReviewInterface;
use Mirasvit\Review\Repository\ReviewRepository;


class Content extends Action
{
    protected $reviewRepository;

    public function __construct(
        ReviewRepository $reviewRepository,
        Context $context
    ) {
        $this->reviewRepository = $reviewRepository;

        parent::__construct($context);
    }

    public function execute()
    {
        $reviewIds = $this->getRequest()->getParam('review_ids');

        $reviews = $this->reviewRepository->getCollection()
            ->addFieldToFilter('main_table.'.ReviewInterface::ID, ['in' => $reviewIds])->getItems();

        if (count($reviews)) {
            $reviewData = [];

            $layout      = $this->resultFactory->create('page')->getLayout();
            $reviewBlock = /*$layout->getBlock('mst.review') ?:*/ $layout->getBlock('mst.product.review');

            if ($reviewBlock) {
                foreach ($reviews as $review) {
                    $review->loadRatingVotes();
                    $reviewData[$review->getId()] = [
                        'title'   => __('Photos from review: ') . ' ' . $review->getTitle(),
                        'content' => $reviewBlock->setReview($review)->setIsShowGallery(false)->toHtml()
                    ];
                }

                $response = [
                    'success' => true,
                    'data'    => $reviewData
                ];
            } else {
                $response = [
                    'success' => false,
                    'message' => "Reviews block not found in layout"
                ];
            }
        } else {
            $response = [
                'success' => false,
                'message' => "Reviews with IDs " . implode(', ', $reviewIds) . " not found"
            ];
        }

        $this->getResponse()->representJson(SerializeService::encode($response));
    }
}
