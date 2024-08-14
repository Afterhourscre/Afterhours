<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Coupongenerator\Model\Coupon;

use Aheadworks\Coupongenerator\Model\Config;
use Aheadworks\Coupongenerator\Api\CouponVariableProcessorInterface;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\App\Area;
use Magento\Store\Api\StoreRepositoryInterface;

/**
 * Class Sender
 * @package Aheadworks\Coupongenerator\Model\Coupon
 */
class Sender
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var TransportBuilder
     */
    private $transportBuilder;

    /**
     * @var CouponVariableProcessorInterface
     */
    private $couponVariableProcessor;

    /**
     * @var StoreRepositoryInterface
     */
    private $storeRepository;

    /**
     * @param Config $config
     * @param TransportBuilder $transportBuilder
     * @param CouponVariableProcessorInterface $couponVariableProcessor
     * @param StoreRepositoryInterface $storeRepository
     */
    public function __construct(
        Config $config,
        TransportBuilder $transportBuilder,
        CouponVariableProcessorInterface $couponVariableProcessor,
        StoreRepositoryInterface $storeRepository
    ) {
        $this->config = $config;
        $this->transportBuilder = $transportBuilder;
        $this->couponVariableProcessor = $couponVariableProcessor;
        $this->storeRepository = $storeRepository;
    }

    /**
     * Send email with coupon code to recipient
     *
     * @param \Magento\SalesRule\Api\Data\CouponInterface $coupon
     * @param string $recipientName
     * @param string $recipientEmail
     * @param int $storeId
     * @return void
     * @throws \Magento\Framework\Exception\MailException
     */
    public function sendCoupon($coupon, $recipientName, $recipientEmail, $storeId)
    {
        /** @var \Magento\Store\Api\Data\StoreInterface $store */
        $store = $this->storeRepository->getById($storeId);

        $sender = $this->config->getEmailSender($store->getWebsiteId());

        $senderName = $this->config->getEmailSenderName($store->getWebsiteId());

        /** @var \Aheadworks\Coupongenerator\Api\Data\CouponVariableInterface $couponVariable */
        $couponVariable = $this->couponVariableProcessor->getCouponVariable($coupon, $store->getId());

        $this->send(
            $this->config->getEmailTemplate($store->getId()),
            [
                'area' => Area::AREA_FRONTEND,
                'store' => $store->getId()
            ],
            $this->prepareTemplateVars(
                [
                    'store'             => $store,
                    'sender_name'       => $senderName,
                    'coupon_code'       => $couponVariable->getCouponCode(),
                    'coupon_discount'   => $couponVariable->getCouponDiscount(),
                    'expiration_date'   => $couponVariable->getCouponExpirationDate(),
                    'uses_per_coupon'   => $couponVariable->getUsesPerCoupon()
                ]
            ),
            $sender,
            $recipientEmail,
            $recipientName
        );
    }

    /**
     * Send email
     *
     * @param string $templateId
     * @param array $templateOptions
     * @param array $templateVars
     * @param string $from
     * @param string $recipientEmail
     * @param string $recipientName
     * @return void
     */
    private function send(
        $templateId,
        array $templateOptions,
        array $templateVars,
        $from,
        $recipientEmail,
        $recipientName
    ) {
        $this->transportBuilder
            ->setTemplateIdentifier($templateId)
            ->setTemplateOptions($templateOptions)
            ->setTemplateVars($templateVars)
            ->setFrom($from)
            ->addTo($recipientEmail, $recipientName)
        ;
        $this->transportBuilder->getTransport()->sendMessage();
    }

    /**
     * Prepare template vars
     *
     * @param array $data
     * @return array
     */
    private function prepareTemplateVars($data)
    {
        $templateVars = [];

        /** @var $store \Magento\Store\Model\Store */
        $store = $data['store'];
        $templateVars['store'] = $store;
        $templateVars['store_name'] = $store->getName();

        if (isset($data['sender_name'])) {
            $templateVars['sender_name'] = $data['sender_name'];
        }
        if (isset($data['coupon_code'])) {
            $templateVars['coupon_code'] = $data['coupon_code'];
        }
        if (isset($data['coupon_discount'])) {
            $templateVars['coupon_discount'] = $data['coupon_discount'];
        }
        if (isset($data['uses_per_coupon']) && $data['uses_per_coupon']) {
            $templateVars['uses_per_coupon'] = $data['uses_per_coupon'];
        }
        if (isset($data['expiration_date']) && !empty($data['expiration_date'])) {
            $templateVars['expiration_date'] = $data['expiration_date'];
        }
        return $templateVars;
    }
}
