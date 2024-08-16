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


namespace Mirasvit\Review\Model\System\Message;

use Magento\Framework\AuthorizationInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\Notification\MessageInterface;
use Mirasvit\Review\Api\Data\ReviewInterface;
use Mirasvit\Review\Model\ConfigProvider;
use Mirasvit\Review\Repository\ReviewRepository;

class PendingReviewMessage implements MessageInterface
{
    const IDENTITY = 'mst_review_pending';

    private $reviewRepository;

    private $urlBuilder;

    private $authorization;
    
    private $configProvider;

    private $pendingReviewsCount = null;

    public function __construct(
        UrlInterface $urlBuilder,
        AuthorizationInterface $authorization,
        ReviewRepository $reviewRepository,
        ConfigProvider $configProvider
    ) {
        $this->urlBuilder       = $urlBuilder;
        $this->authorization    = $authorization;
        $this->reviewRepository = $reviewRepository;
        $this->configProvider   = $configProvider;
    }

    public function getIdentity()
    {
        return self::IDENTITY;
    }

    public function isDisplayed()
    {
        return $this->configProvider->isAdminNotificationEnabled() 
            && $this->authorization->isAllowed('Mirasvit_Review::review') 
            && (bool)$this->getPendingReviewsCount();
    }

    public function getText()
    {
        $message = __('Advanced Reviews: You have %1 pending review(s).', $this->getPendingReviewsCount()) . ' ';
        $url     = $this->urlBuilder->getUrl('mst_review/review');
        $message .= __('Please go to <a href="%1">Reviews</a> to check pending reviews.', $url);

        return $message;
    }

    public function getSeverity()
    {
        return self::SEVERITY_NOTICE;
    }

    private function getPendingReviewsCount(): int
    {
        if (is_null($this->pendingReviewsCount)) {
            $this->pendingReviewsCount = $this->reviewRepository
                ->getCollection(false)
                ->addFieldToFilter('main_table.status_id', ReviewInterface::STATUS_PENDING)
                ->getSize();
        }

        return (int)$this->pendingReviewsCount;
    }
}
