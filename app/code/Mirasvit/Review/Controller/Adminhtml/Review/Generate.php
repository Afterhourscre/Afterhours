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


namespace Mirasvit\Review\Controller\Adminhtml\Review;


use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\ForwardFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Registry;
use Magento\Review\Model\RatingFactory;
use Mirasvit\Core\Service\SerializeService;
use Mirasvit\Review\Controller\Adminhtml\ReviewAbstract;
use Mirasvit\Review\Repository\ReviewRepository;
use Mirasvit\Review\Service\AutowriteService;

class Generate extends ReviewAbstract
{
    private $autowriteService;

    public function __construct(
        AutowriteService $autowriteService,
        RatingFactory $ratingFactory,
        ReviewRepository $repository,
        ForwardFactory $resultForwardFactory,
        Registry $registry,
        Context $context
    ) {
        $this->autowriteService = $autowriteService;

        parent::__construct(
            $ratingFactory,
            $repository,
            $resultForwardFactory,
            $registry,
            $context
        );
    }

    public function execute()
    {
        $responseData = [
            'success' => true,
            'data'    => [],
            'message' => ''
        ];

        $result = $this->resultFactory->create(ResultFactory::TYPE_JSON);

        $productId = (int)$this->getRequest()->getParam('product_id');

        if (!$productId) {
            $responseData['success'] = false;
            $responseData['message'] = (string)__('Product ID is not set');

            return $this->getResponse()->representJson(SerializeService::encode($responseData));
        }

        $instructions = (string)$this->getRequest()->getParam('instruction');
        $storeId      = (int)$this->getRequest()->getParam('store_id');

        try {
            $responseData['data'] = $this->autowriteService->generateReview($productId, $instructions, $storeId);
        } catch (\Exception $e) {
            $responseData['success'] = false;
            $responseData['message'] = $e->getMessage();
        }

        return $this->getResponse()->representJson(SerializeService::encode($responseData));
    }
}
