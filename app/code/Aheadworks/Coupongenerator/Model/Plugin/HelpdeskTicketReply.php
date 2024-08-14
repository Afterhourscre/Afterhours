<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Coupongenerator\Model\Plugin;

use Aheadworks\Helpdesk\Controller\Adminhtml\Ticket\Reply as TicketReply;
use Aheadworks\Coupongenerator\Api\CouponManagerInterface;
use Aheadworks\Coupongenerator\Api\CouponVariableProcessorInterface;
use Aheadworks\Helpdesk\Api\TicketRepositoryInterface;
use Magento\Framework\ObjectManagerInterface;

/**
 * Class HelpdeskTicketReply
 * @package Aheadworks\Coupongenerator\Model\Plugin
 * @codeCoverageIgnore
 */
class HelpdeskTicketReply
{
    /**
     * @var CouponManagerInterface
     */
    private $couponManager;

    /**
     * @var CouponVariableProcessorInterface
     */
    private $couponProcessor;

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @param CouponManagerInterface $couponManager
     * @param CouponVariableProcessorInterface $couponProcessor
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(
        CouponManagerInterface $couponManager,
        CouponVariableProcessorInterface $couponProcessor,
        ObjectManagerInterface $objectManager
    ) {
        $this->couponManager = $couponManager;
        $this->couponProcessor = $couponProcessor;
        $this->objectManager = $objectManager;
    }

    /**
     * Add coupon info to ticket content
     *
     * @param TicketReply $subject
     * @return array
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function beforeExecute(TicketReply $subject)
    {
        $data = $subject->getRequest()->getPostValue();

        if (isset($data['awcg_rule_id'])
            && $data['awcg_rule_id'] > 0
            && isset($data['content'])
            && isset($data['ticket_id'])
        ) {
            /** @var TicketRepositoryInterface $ticketRepository */
            $ticketRepository = $this->objectManager->create(TicketRepositoryInterface::class);

            /** @var \Aheadworks\Helpdesk\Api\Data\TicketInterface $ticket */
            $ticket = $ticketRepository->getById($data['ticket_id']);

            if ($ticket->getCustomerId()) {
                /** @var \Aheadworks\Coupongenerator\Api\Data\CouponGenerationResultInterface $result */
                $result = $this->couponManager->generateForCustomer(
                    $data['awcg_rule_id'],
                    $ticket->getCustomerId(),
                    false
                );
            } else {
                /** @var \Aheadworks\Coupongenerator\Api\Data\CouponGenerationResultInterface $result */
                $result = $this->couponManager->generateForEmail(
                    $data['awcg_rule_id'],
                    $ticket->getCustomerEmail(),
                    false
                );
            }

            /** @var \Magento\SalesRule\Api\Data\CouponInterface|null $coupon */
            $coupon = $result->getCoupon();
            if ($coupon) {
                /** @var \Aheadworks\Coupongenerator\Api\Data\CouponVariableInterface $couponVariable */
                $couponVariable = $this->couponProcessor->getCouponVariable($coupon, $ticket->getStoreId());

                $content = $data['content'];
                $content .= PHP_EOL . __('Your coupon code is %1.', $couponVariable->getCouponCode());
                $content .= PHP_EOL . __('The coupon will give you %1 discount.', $couponVariable->getCouponDiscount());
                if ($coupon->getExpirationDate()) {
                    $content .= PHP_EOL . __('The coupon expires at %1.', $couponVariable->getCouponExpirationDate());
                }
                if ($coupon->getUsageLimit() && $coupon->getUsageLimit() > 1) {
                    $content .= PHP_EOL . __('The coupon can be used %1 times.', $couponVariable->getUsesPerCoupon());
                }

                $subject->getRequest()->setPostValue('content', $content);
            }
        }

        return [];
    }
}
