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


namespace Mirasvit\Review\Service\Email;


use Magento\Backend\App\Area\FrontNameResolver;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Review\Model\RatingFactory;
use Magento\Store\Model\StoreManagerInterface;
use Mirasvit\Review\Api\Data\ReviewInterface;
use Mirasvit\Review\Model\ConfigProvider;
use Mirasvit\Review\Repository\ReviewMediaRepository;
use Mirasvit\Review\Repository\ReviewRepository;

class NotificationService
{
    private $transportBuilder;

    private $configProvider;

    private $reviewRepository;

    private $reviewMediaRepository;

    private $storeManager;

    private $ratingFactory;

    public function __construct(
        TransportBuilder $transportBuilder,
        ConfigProvider $configProvider,
        ReviewRepository $reviewRepository,
        ReviewMediaRepository $reviewMediaRepository,
        StoreManagerInterface $storeManager,
        RatingFactory $ratingFactory
    ) {
        $this->transportBuilder      = $transportBuilder;
        $this->configProvider        = $configProvider;
        $this->reviewRepository      = $reviewRepository;
        $this->reviewMediaRepository = $reviewMediaRepository;
        $this->storeManager          = $storeManager;
        $this->ratingFactory         = $ratingFactory;
    }

    public function notifyPendingReview(int $reviewId, bool $isTest = false)
    {
        if (!$this->configProvider->isEmailNotificationsForPendingReviewsEnabled()) {
            return;
        }

        $emails = $this->configProvider->getPendingReviewNotificationEmails();

        if (!$emails) {
            return;
        }

        $review = $this->reviewRepository->get($reviewId);

        if (!$review) {
            return;
        }

        $this->transportBuilder
            ->setTemplateIdentifier('mst_review_pending')
            ->setTemplateOptions([
                'area'  => FrontNameResolver::AREA_CODE,
                'store' => 0,
            ])->setTemplateVars($this->prepareReviewVars($review, $isTest))
            ->setFrom($this->configProvider->getSender())
            ->addTo($emails);

        $this->transportBuilder->getTransport()->sendMessage();
    }

    private function prepareReviewVars(ReviewInterface $review, bool $isTest = false): array
    {
        $subject = (string)__('You have new pending review for product "%1"', $review->getProduct()->getName());

        if ($isTest) {
            $subject .= ' (TEST NOTIFICATION)';
        }

        $vars = [
            'subject'                 => $subject,
            ReviewInterface::NICKNAME => $review->getNickname(),
            ReviewInterface::TITLE    => $review->getTitle(),
            ReviewInterface::DETAIL   => $review->getDetail(),
        ];

        $ratingData = $this->ratingFactory->create()->getReviewSummary($review->getId());

        if ($ratingData) {
            $vars['rating'] = ceil($ratingData->getSum() / $ratingData->getCount());
        }

        if ($this->configProvider->displayProsAndCons()) {
            $vars[ReviewInterface::PROS] = $review->getPros() ? nl2br($review->getPros()) : '-';
            $vars[ReviewInterface::CONS] = $review->getCons() ? nl2br($review->getCons()) : '-';
        }

        if ($this->configProvider->isMediaEnabled()) {
            $vars['images'] = '';

            $images = $this->reviewMediaRepository->getByReview((int)$review->getId());

            if ($images) {
                foreach ($images as $image) {
                    $url = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA)
                        . 'mst_review/'
                        . $image->getReviewId() . '/'
                        . $image->getValue();

                    $vars['images'] .= "<img src=\"{$url}\" width=\"300\" style='margin-right: 5px; margin-bottom: 5px'/>";
                }
            }
        }

        return $vars;
    }
}
