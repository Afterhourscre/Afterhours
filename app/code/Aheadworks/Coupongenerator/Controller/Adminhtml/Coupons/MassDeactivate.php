<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Coupongenerator\Controller\Adminhtml\Coupons;

/**
 * Class MassDeactivate
 * @package Aheadworks\Coupongenerator\Controller\Adminhtml\Coupons
 */
class MassDeactivate extends \Magento\Backend\App\Action
{
    /**
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Aheadworks_Coupongenerator::manage_coupons';

    /**
     * @var \Magento\Framework\EntityManager\EntityManager
     */
    private $entityManager;

    /**
     * @var \Aheadworks\Coupongenerator\Model\ResourceModel\Coupon\CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var \Aheadworks\Coupongenerator\Api\Data\CouponInterfaceFactory
     */
    private $couponInterfaceFactory;

    /**
     * @var \Magento\Ui\Component\MassAction\Filter
     */
    private $filter;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Ui\Component\MassAction\Filter $filter
     * @param \Aheadworks\Coupongenerator\Model\ResourceModel\Coupon\CollectionFactory $collectionFactory
     * @param \Magento\Framework\EntityManager\EntityManager $entityManager
     * @param \Aheadworks\Coupongenerator\Api\Data\CouponInterfaceFactory $couponInterfaceFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Ui\Component\MassAction\Filter $filter,
        \Aheadworks\Coupongenerator\Model\ResourceModel\Coupon\CollectionFactory $collectionFactory,
        \Magento\Framework\EntityManager\EntityManager $entityManager,
        \Aheadworks\Coupongenerator\Api\Data\CouponInterfaceFactory $couponInterfaceFactory
    ) {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->entityManager = $entityManager;
        $this->couponInterfaceFactory = $couponInterfaceFactory;

        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        try {
            $collection = $this->filter->getCollection($this->collectionFactory->create());
            $count = 0;
            foreach ($collection->getAllIds() as $couponId) {
                /** @var \Aheadworks\Coupongenerator\Api\Data\CouponInterface $coupon */
                $coupon = $this->couponInterfaceFactory->create();
                $this->entityManager->load($coupon, $couponId);

                if ($coupon->getId()) {
                    $coupon->setIsDeactivated(true);
                    $this->entityManager->save($coupon);
                    $count++;
                }
            }
            $this->messageManager->addSuccessMessage(__('A total of %1 coupon(s) have been deactivated', $count));
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e, __('Something went wrong while perform mass action'));
        }

        return $resultRedirect->setPath('*/*/index');
    }
}
