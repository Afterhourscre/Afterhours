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
use Mirasvit\Core\Service\SerializeService;
use Mirasvit\Review\Repository\ReviewRepository;

class Ajax extends Action
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
        $page      = $this->getRequest()->getParam('p');
        $limit     = $this->getRequest()->getParam('limit');
        $sortOrder = $this->getRequest()->getParam('sort_order');

        list($field, $dir) = explode('-', $sortOrder);

        $collection = $this->reviewRepository->getCollection()
            ->setSortOrder($field, $dir)
            ->setPageSize($limit)
            ->setCurPage($page);

        $result = [
            'blocks'  => [],
            'loaded'  => 0,
            'success' => true,
            'message' => ''
        ];

        $layout      = $this->resultFactory->create('page')->getLayout();
        $reviewBlock = $layout->getBlock('mst.review_card');

        foreach ($collection as $review) {
            $result['blocks'][] = $reviewBlock->setReview($review)->toHtml();
            $result['loaded']++;
        }

        $this->getResponse()->representJson(SerializeService::encode($result));
    }
}
