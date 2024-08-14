<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Coupongenerator\Model\Plugin;

use Magento\Framework\App\RequestInterface;
use Magento\Customer\Controller\Adminhtml\Index\Save;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Message\ManagerInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Aheadworks\Coupongenerator\Api\CouponManagerInterface;

/**
 * Class AdminhtmlCustomerSave
 * @package Aheadworks\Coupongenerator\Model\Plugin
 * @codeCoverageIgnore
 */
class AdminhtmlCustomerSave
{
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private $request;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    private $messageManager;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var \Aheadworks\Coupongenerator\Api\CouponManagerInterface
     */
    private $couponManager;

    /**
     * @param RequestInterface $request
     * @param ManagerInterface $messageManager
     * @param CustomerRepositoryInterface $customerRepository
     * @param CouponManagerInterface $couponManager
     */
    public function __construct(
        RequestInterface $request,
        ManagerInterface $messageManager,
        CustomerRepositoryInterface $customerRepository,
        CouponManagerInterface $couponManager
    ) {
        $this->request = $request;
        $this->messageManager = $messageManager;
        $this->customerRepository = $customerRepository;
        $this->couponManager = $couponManager;
    }

    /**
     * Save additional data after "Save customer" button is pressed
     *
     * @param \Magento\Customer\Controller\Adminhtml\Index\Save $subject
     * @param \Magento\Backend\Model\View\Result\Redirect $resultRedirect
     * @return \Magento\Backend\Model\View\Result\Redirect
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterExecute(Save $subject, Redirect $resultRedirect)
    {
        $ruleId = $this->request->getParam('rule_id');

        if ($ruleId) {
            $isSendEmail = filter_var($this->request->getParam('send_email_with_coupon'), FILTER_VALIDATE_BOOLEAN);

            $postData = $this->request->getPostValue();
            $customerId = isset($postData['customer']['entity_id'])
                ? $postData['customer']['entity_id']
                : null;

            try {
                /** @var \Magento\Customer\Api\Data\CustomerInterface $customer */
                $customer = $this->customerRepository->getById($customerId);

                /** @var \Aheadworks\Coupongenerator\Api\Data\CouponGenerationResultInterface $result */
                $result = $this->couponManager->generateForCustomer($ruleId, $customer->getId(), $isSendEmail);

                if ($result->getCoupon()) {
                    foreach ($result->getMessages() as $message) {
                        $this->messageManager->addSuccessMessage($message);
                    }
                }
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }
        }

        return $resultRedirect;
    }
}
