<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Coupongenerator\Controller\Adminhtml\Coupon;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class MassGenerateSendCoupon
 * @package Aheadworks\Coupongenerator\Controller\Adminhtml\Coupon
 */
class MassGenerateSendCoupon extends \Magento\Backend\App\Action
{
    /**
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Aheadworks_Coupongenerator::generate_coupons';

    /**
     * @var \Magento\Customer\Model\ResourceModel\Customer\Collection
     */
    private $collection;

    /**
     * @var \Magento\Ui\Component\MassAction\Filter
     */
    private $filter;

    /**
     * @var \Aheadworks\Coupongenerator\Api\CouponManagerInterface
     */
    private $couponManager;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Ui\Component\MassAction\Filter $filter
     * @param \Magento\Customer\Model\ResourceModel\Customer\Collection $collection
     * @param \Aheadworks\Coupongenerator\Api\CouponManagerInterface $couponManager
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Ui\Component\MassAction\Filter $filter,
        \Magento\Customer\Model\ResourceModel\Customer\Collection $collection,
        \Aheadworks\Coupongenerator\Api\CouponManagerInterface $couponManager,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
    ) {
        $this->collection = $collection;
        $this->filter = $filter;
        $this->couponManager = $couponManager;
        $this->customerRepository = $customerRepository;

        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($this->getRequest()->getPostValue()) {
            try {
                $this->collection = $this->filter->getCollection($this->collection);
                $ruleId = $this->getRequest()->getParam('rule_id');

                if (!$ruleId) {
                    throw new LocalizedException(__('Please select rule'));
                }

                $completed = 0;
                $failed = 0;
                foreach ($this->collection->getAllIds() as $customerId) {
                    try {
                        $customer = $this->customerRepository->getById($customerId);

                        try {
                            /** @var \Aheadworks\Coupongenerator\Api\Data\CouponGenerationResultInterface $result */
                            $result = $this->couponManager->generateForCustomer($ruleId, $customer->getId(), true);
                            if ($result->getCoupon()) {
                                foreach ($result->getMessages() as $message) {
                                    $this->messageManager->addSuccessMessage($message);
                                }
                            }
                            $completed++;
                        } catch (LocalizedException $e) {
                            $failed++;
                        }
                    } catch (NoSuchEntityException $e) {
                        $failed++;
                    }
                }

                if ($completed == 0) {
                    $this->messageManager->addErrorMessage(
                        __('All selected customers is not valid for the rule')
                    );
                } elseif ($completed > 0 && $failed > 0) {
                    $this->messageManager->addErrorMessage(
                        __('Some selected customers is not valid for the rule')
                    );
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
