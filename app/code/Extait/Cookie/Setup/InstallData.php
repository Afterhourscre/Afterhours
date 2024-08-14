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

namespace Extait\Cookie\Setup;

use Extait\Cookie\Api\Data\CategoryInterface;
use Extait\Cookie\Api\Data\CategoryRepositoryInterface;
use Extait\Cookie\Api\Data\CookieInterface;
use Extait\Cookie\Api\Data\CookieRepositoryInterface;
use Extait\Cookie\Model\CategoryFactory;
use Extait\Cookie\Model\CookieFactory;
use Magento\Customer\Model\Customer;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class InstallData implements InstallDataInterface
{
    /**
     * @var \Extait\Cookie\Model\CookieFactory
     */
    protected $cookieFactory;

    /**
     * @var \Extait\Cookie\Model\CategoryFactory
     */
    protected $categoryFactory;

    /**
     * @var \Extait\Cookie\Api\Data\CookieRepositoryInterface
     */
    protected $cookieRepository;

    /**
     * @var \Extait\Cookie\Api\Data\CategoryRepositoryInterface
     */
    protected $categoryRepository;

    /**
     * @var \Magento\Customer\Setup\CustomerSetupFactory
     */
    protected $customerSetupFactory;

    /**
     * InstallData constructor.
     *
     * @param \Extait\Cookie\Model\CookieFactory $cookieFactory
     * @param \Extait\Cookie\Model\CategoryFactory $categoryFactory
     * @param \Extait\Cookie\Api\Data\CookieRepositoryInterface $cookieRepository
     * @param \Extait\Cookie\Api\Data\CategoryRepositoryInterface $categoryRepository
     * @param \Magento\Customer\Setup\CustomerSetupFactory $customerSetupFactory
     */
    public function __construct(
        CookieFactory $cookieFactory,
        CategoryFactory $categoryFactory,
        CookieRepositoryInterface $cookieRepository,
        CategoryRepositoryInterface $categoryRepository,
        CustomerSetupFactory $customerSetupFactory
    ) {
        $this->cookieFactory = $cookieFactory;
        $this->categoryFactory = $categoryFactory;
        $this->cookieRepository = $cookieRepository;
        $this->categoryRepository = $categoryRepository;
        $this->customerSetupFactory = $customerSetupFactory;
    }

    /**
     * Install Data Process.
     *
     * @param \Magento\Framework\Setup\ModuleDataSetupInterface $setup
     * @param \Magento\Framework\Setup\ModuleContextInterface $context
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        // System cookies.
        $systemCategory = $this->createSystemCategory();
        $this->addSystemCookies($systemCategory);

        // Marketing cookies.
        $marketingCategory = $this->createMarketingCategory();
        $this->addMarketingCookies($marketingCategory);

        $this->addCookiesAttribute($setup);

        $setup->endSetup();
    }

    /**
     * Create the System category.
     *
     * @return \Extait\Cookie\Model\Category
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function createSystemCategory()
    {
        $category = $this->categoryFactory->create();

        $category->setName('System');
        $category->setDescription(
            'Necessary cookies enable core functionality of the website. Without these cookies the website ' .
            'can not function properly. They help to make a website usable by enabling basic functionality.'
        );
        $category->setIsSystem(1);

        $this->categoryRepository->save($category);

        return $category;
    }

    /**
     * Add system cookies to the system category.
     *
     * @param \Extait\Cookie\Api\Data\CategoryInterface $category
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function addSystemCookies(CategoryInterface $category)
    {
        $cookies = [
            'PHPSESSID' => [
                CookieInterface::DESCRIPTION => 'To store the logged in user\'s username and a 128bit encrypted key. ' .
                    'This information is required to allow a user to stay logged in to a web site without needing to ' .
                    'submit their username and password for each page visited. Without this cookie, a user is unable ' .
                    'to proceed to areas of the web site that require authenticated access.',
            ],
            'private_content_version' => [
                CookieInterface::DESCRIPTION => 'Appends a random, unique number and time to pages with customer ' .
                    'content to prevent them from being cached on the server.',
            ],
            'persistent_shopping_cart' => [
                CookieInterface::DESCRIPTION => 'Stores the key (ID) of persistent cart to make it possible to ' .
                    'restore the cart for an anonymous shopper.',
            ],
            'form_key' => [
                CookieInterface::DESCRIPTION => 'A security measure that appends a random string to all form ' .
                    'submissions to protect the data from Cross-Site Request Forgery (CSRF).',
            ],
            'store' => [
                CookieInterface::DESCRIPTION => 'Tracks the specific store view / locale selected by the shopper.',
            ],
            'login_redirect' => [
                CookieInterface::DESCRIPTION => 'Preserves the destination page the customer was navigating to ' .
                    'before being directed to log in.',
            ],
            'mage-messages' => [
                CookieInterface::DESCRIPTION => 'Tracks error messages and other notifications that are shown to ' .
                    'the user, such as the cookie consent message, and various error messages, The message is ' .
                    'deleted from the cookie after it is shown to the shopper.',
            ],
            'mage-cache-storage' => [
                CookieInterface::DESCRIPTION => 'Local storage of visitor-specific content that enables ' .
                    'e-commerce functions.',
            ],
            'mage-cache-storage-section-invalidation' => [
                CookieInterface::DESCRIPTION => 'Forces local storage of specific content sections that should ' .
                    'be invalidated.',
            ],
            'mage-cache-sessid' => [
                CookieInterface::DESCRIPTION => 'The value of this cookie triggers the cleanup of local cache storage.',
            ],
            'product_data_storage' => [
                CookieInterface::DESCRIPTION => 'Stores configuration for product data related to ' .
                    'Recently Viewed / Compared Products.',
            ],
            'user_allowed_save_cookie' => [
                CookieInterface::DESCRIPTION => 'Indicates if the shopper allows cookies to be saved.',
            ],
            'mage-translation-storage' => [
                CookieInterface::DESCRIPTION => 'Stores translated content when requested by the shopper.',
            ],
            'mage-translation-file-version' => [
                CookieInterface::DESCRIPTION => 'Stores the file version of translated content.',
            ],
            'extait_allowed_categories' => [
                CookieInterface::DESCRIPTION => 'Stores category IDs which a user allows using.',
            ],
            'extait_allowed_cookies' => [
                CookieInterface::DESCRIPTION => 'Stores cookie names which a user allows using.',
            ],
        ];

        foreach ($cookies as $name => $values) {
            $cookie = $this->cookieFactory->create();

            $cookie->setName($name);
            $cookie->setDescription($values[CookieInterface::DESCRIPTION]);
            $cookie->setIsSystem(1);
            $cookie->setCategoryId($category->getId());

            $this->cookieRepository->save($cookie);
        }
    }

    /**
     * Create the Marketing category.
     *
     * @return \Extait\Cookie\Model\Category
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function createMarketingCategory()
    {
        $category = $this->categoryFactory->create();

        $category->setName('Marketing');
        $category->setDescription(
            'Marketing cookies are used to track and collect visitors actions on the ' .
            'website. Cookies store user data and behaviour information, which allows advertising services to target ' .
            'more audience groups. Also more customized user experience can be provided according to collected ' .
            'information.'
        );
        $category->setIsSystem(0);

        $this->categoryRepository->save($category);

        return $category;
    }

    /**
     * Add cookies to the marketing category.
     *
     * @param \Extait\Cookie\Api\Data\CategoryInterface $category
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function addMarketingCookies(CategoryInterface $category)
    {
        $cookies = [
            'section_data_ids' => [
                CookieInterface::DESCRIPTION => 'Stores customer-specific information related to shopper-initiated ' .
                    'actions such as display wish list, checkout information, etc.',
            ],
            'recently_viewed_product' => [
                CookieInterface::DESCRIPTION => 'Stores product IDs of recently viewed products for easy navigation.',
            ],
            'recently_viewed_product_previous' => [
                CookieInterface::DESCRIPTION => 'Stores product IDs of recently previously viewed products for easy ' .
                    'navigation.',
            ],
            'recently_compared_product' => [
                CookieInterface::DESCRIPTION => 'Stores product IDs of recently compared products.',
            ],
            'recently_compared_product_previous' => [
                CookieInterface::DESCRIPTION => 'Stores product IDs of previously compared products for easy ' .
                    'navigation.',
            ],
        ];

        foreach ($cookies as $name => $values) {
            $cookie = $this->cookieFactory->create();

            $cookie->setName($name);
            $cookie->setDescription($values[CookieInterface::DESCRIPTION]);
            $cookie->setIsSystem(0);
            $cookie->setCategoryId($category->getId());

            $this->cookieRepository->save($cookie);
        }
    }

    /**
     * @param ModuleDataSetupInterface $setup
     */
    protected function addCookiesAttribute(ModuleDataSetupInterface $setup)
    {
        $customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);

        $customerSetup->addAttribute(
            Customer::ENTITY,
            'extait_cookie_categories',
            [
                'type' => 'static',
                'label' => 'Cookie Categories',
                'input' => 'hidden',
                'required' => false,
                'visible' => false,
                'user_defined' => false,
                'position' => 600,
                'system' => 0,
            ]
        );
    }
}
