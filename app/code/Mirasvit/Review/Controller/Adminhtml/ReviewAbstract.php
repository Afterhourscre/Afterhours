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


namespace Mirasvit\Review\Controller\Adminhtml;


use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\ForwardFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Registry;
use Magento\Review\Model\RatingFactory;
use Mirasvit\Review\Api\Data\ReviewInterface;
use Mirasvit\Review\Repository\ReviewRepository;


abstract class ReviewAbstract extends Action
{
    protected $ratingFactory;

    protected $reviewRepository;

    protected $resultForwardFactory;

    protected $registry;

    private   $context;

    private   $session;

    public function __construct(
        RatingFactory $ratingFactory,
        ReviewRepository $repository,
        ForwardFactory $resultForwardFactory,
        Registry $registry,
        Context $context
    ) {
        $this->ratingFactory        = $ratingFactory;
        $this->reviewRepository     = $repository;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->registry             = $registry;
        $this->context              = $context;
        $this->session              = $context->getSession();

        parent::__construct($context);
    }

    protected function initPage(ResultInterface $resultPage): ResultInterface
    {
        $resultPage->setActiveMenu('Mirasvit_Review::review');

        $resultPage->getConfig()->getTitle()->prepend((string)__('Advanced Reviews'));
        $resultPage->getConfig()->getTitle()->prepend((string)__('Reviews'));

        return $resultPage;
    }

    protected function initModel(): ?ReviewInterface
    {
        $model = $this->reviewRepository->create();

        if ($id = $this->getRequest()->getParam(ReviewInterface::ID)) {
            $model = $this->reviewRepository->get((int)$id);
            $this->registry->register('review_data', $model);
        }

        return $model;
    }

    protected function _isAllowed(): bool
    {
        return $this->context->getAuthorization()->isAllowed('Mirasvit_Review::review');
    }
}
