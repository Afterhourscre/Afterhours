<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the commercial license
 * that is bundled with this package in the file LICENSE.txt.
 *
 * @category Extait
 * @package Extait_Cookie
 * @copyright Copyright (c) 2016-2018 Extait, Inc. (http://www.extait.com)
 */

namespace Extait\Cookie\Helper;

use Extait\Cookie\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Framework\Json\DecoderInterface;
use Magento\Framework\Stdlib\Cookie\PhpCookieManager;

class CategoryManager extends AbstractHelper
{
    /**
     * @var \Magento\Framework\App\Http\Context
     */
    protected $httpContext;

    /**
     * @var \Extait\Cookie\Model\ResourceModel\Category\CollectionFactory
     */
    protected $categoryCollectionFactory;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var \Magento\Framework\Json\DecoderInterface
     */
    protected $decoder;

    /**
     * @var \Magento\Framework\Stdlib\Cookie\PhpCookieManager
     */
    protected $phpCookieManager;

    /**
     * CookieManager constructor.
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param \Extait\Cookie\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Magento\Framework\Json\DecoderInterface $decoder
     * @param \Magento\Framework\Stdlib\Cookie\PhpCookieManager $phpCookieManager
     */
    public function __construct(
        Context $context,
        HttpContext $httpContext,
        CategoryCollectionFactory $categoryCollectionFactory,
        CustomerRepositoryInterface $customerRepository,
        DecoderInterface $decoder,
        PhpCookieManager $phpCookieManager
    ) {
        parent::__construct($context);

        $this->httpContext = $httpContext;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->customerRepository = $customerRepository;
        $this->decoder = $decoder;
        $this->phpCookieManager = $phpCookieManager;
    }

    /**
     * @return \Extait\Cookie\Api\Data\CategoryInterface[]|\Magento\Framework\DataObject[]
     */
    public function getAllCategories()
    {
        return $this->categoryCollectionFactory->create()->getItems();
    }

    /**
     * Get user allowed categories IDs.
     *
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getUserAllowedCategoriesIDs()
    {
        $customerID = $this->httpContext->getValue('logged_in_customer_id');
        $categoriesIDs = $this->categoryCollectionFactory->create()->getAllIds();
        $sessionCategoriesIDs = (array)json_decode($this->phpCookieManager->getCookie('extait_allowed_categories'));

        if (!empty($customerID)) {
            $customer = $this->customerRepository->getById($customerID);
            $customerCategories = $customer->getCustomAttribute('extait_cookie_categories');

            if (!empty($customerCategories->getValue())) {
                $categoriesIDs = $this->decode($customerCategories->getValue());
            }
        } elseif (!empty($sessionCategoriesIDs)) {
            $categoriesIDs = $sessionCategoriesIDs;
        }

        return $categoriesIDs;
    }

    /**
     * @param $value
     *
     * @return mixed
     */
    public function decode($value)
    {
        return $this->decoder->decode($value);
    }
}
