<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Coupongenerator\Model\Newsletter;

use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Aheadworks\Coupongenerator\Api\CouponVariableManagerInterfaceFactory;

/**
 * Subscriber model
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Subscriber extends \Magento\Newsletter\Model\Subscriber
{
    /**
     * @var \Aheadworks\Coupongenerator\Api\CouponVariableManagerInterfaceFactory
     */
    protected $couponVariableManagerFactory;

    /**
     * @var \Aheadworks\Coupongenerator\Api\CouponVariableManagerInterface|null
     */
    protected $couponVariableManager;

    /**
     * @param CouponVariableManagerInterfaceFactory $couponVariableManagerFactory
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Newsletter\Helper\Data $newsletterData
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Customer\Model\Session $customerSession
     * @param CustomerRepositoryInterface $customerRepository
     * @param AccountManagementInterface $customerAccountManagement
     * @param \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param array $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        CouponVariableManagerInterfaceFactory $couponVariableManagerFactory,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Newsletter\Helper\Data $newsletterData,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Model\Session $customerSession,
        CustomerRepositoryInterface $customerRepository,
        AccountManagementInterface $customerAccountManagement,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            $newsletterData,
            $scopeConfig,
            $transportBuilder,
            $storeManager,
            $customerSession,
            $customerRepository,
            $customerAccountManagement,
            $inlineTranslation,
            $resource,
            $resourceCollection,
            $data
        );

        $this->couponVariableManagerFactory = $couponVariableManagerFactory;
    }

    /**
     * Generate coupon code
     *
     * @param int|null $ruleId
     * @param string|null $alias
     * @return void
     * @codeCoverageIgnore
     */
    public function generateCoupon($ruleId = null, $alias = null)
    {
        if ($ruleId) {
            if ($this->couponVariableManager === null) {
                $this->initCouponVariableManager();
            }
            $this->couponVariableManager->generateCoupon($ruleId, $alias);
        }
    }

    /**
     * Get coupon code
     *
     * @param string|null $alias
     * @return string|null
     * @codeCoverageIgnore
     */
    public function getCouponCode($alias = null)
    {
        if ($this->couponVariableManager) {
            return $this->couponVariableManager->getCouponCode($alias);
        }

        return null;
    }

    /**
     * Get coupon expiration date
     *
     * @param string|null $alias
     * @return string|null
     * @codeCoverageIgnore
     */
    public function getCouponExpirationDate($alias = null)
    {
        if ($this->couponVariableManager) {
            return $this->couponVariableManager->getCouponExpirationDate($alias);
        }

        return null;
    }

    /**
     * Get coupon discount
     *
     * @param string|null $alias
     * @return string|null
     * @codeCoverageIgnore
     */
    public function getCouponDiscount($alias = null)
    {
        if ($this->couponVariableManager) {
            return $this->couponVariableManager->getCouponDiscount($alias);
        }

        return null;
    }

    /**
     * Get uses per coupon
     *
     * @param string|null $alias
     * @return string|null
     * @codeCoverageIgnore
     */
    public function getUsesPerCoupon($alias = null)
    {
        if ($this->couponVariableManager) {
            return $this->couponVariableManager->getUsesPerCoupon($alias);
        }

        return null;
    }

    /**
     * Init coupon variable manager
     *
     * @return void
     * @codeCoverageIgnore
     */
    protected function initCouponVariableManager()
    {
        $this->couponVariableManager = $this->couponVariableManagerFactory->create();

        if ($this->getCustomerId()) {
            $this->couponVariableManager
                ->setRecipientByCustomerId($this->getCustomerId())
                ->setStoreId($this->getStoreId())
            ;
        } else {
            $this->couponVariableManager
                ->setRecipientByEmail($this->getSubscriberEmail())
                ->setStoreId($this->getStoreId())
            ;
        }
    }
}
