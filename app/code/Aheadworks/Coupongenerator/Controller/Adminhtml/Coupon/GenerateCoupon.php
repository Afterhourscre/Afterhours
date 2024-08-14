<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Coupongenerator\Controller\Adminhtml\Coupon;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Aheadworks\Coupongenerator\Api\CouponManagerInterface;

/**
 * Class GenerateCoupon
 * @package Aheadworks\Coupongenerator\Controller\Adminhtml\Coupon
 */
class GenerateCoupon extends \Magento\Backend\App\Action
{
    /**
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Aheadworks_Coupongenerator::generate_coupons';

    /**
     * @var \Aheadworks\Coupongenerator\Api\CouponManagerInterface
     */
    private $couponManager;

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param CouponManagerInterface $couponManager
     * @param CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        CouponManagerInterface $couponManager,
        CustomerRepositoryInterface $customerRepository
    ) {
        $this->couponManager = $couponManager;
        $this->customerRepository = $customerRepository;

        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();
        if ($data) {
            try {
                $recipientEmail = isset($data['recipient_email']) ? $data['recipient_email'] : false;
                $ruleId = isset($data['rule_id']) ? $data['rule_id'] : false;
                $isSendEmail = isset($data['send_email_to_recipient']) ? (bool)$data['send_email_to_recipient'] : false;

                if (!$ruleId) {
                    throw new LocalizedException(__('Please select rule'));
                }
                if (!\Zend_Validate::is($recipientEmail, 'EmailAddress')) {
                    throw new LocalizedException(__('Please correct the email address: %1', $recipientEmail));
                }

                try {
                    $customer = $this->customerRepository->get($recipientEmail);

                    /** @var \Aheadworks\Coupongenerator\Api\Data\CouponGenerationResultInterface $result */
                    $result = $this->couponManager->generateForCustomer($ruleId, $customer->getId(), $isSendEmail);
                } catch (NoSuchEntityException $e) {
                    /** @var \Aheadworks\Coupongenerator\Api\Data\CouponGenerationResultInterface $result */
                    $result = $this->couponManager->generateForEmail($ruleId, $recipientEmail, $isSendEmail);
                }

                if ($result->getCoupon()) {
                    foreach ($result->getMessages() as $message) {
                        $this->messageManager->addSuccessMessage($message);
                    }
                }
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage(
                    $e,
                    __('Something went wrong while generated the coupon code')
                );
            }
        }

        return $resultRedirect->setPath('*/*/generate');
    }
}
