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
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;

class UpgradeData implements UpgradeDataInterface
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
     * InstallData constructor.
     *
     * @param \Extait\Cookie\Model\CookieFactory $cookieFactory
     * @param \Extait\Cookie\Model\CategoryFactory $categoryFactory
     * @param \Extait\Cookie\Api\Data\CookieRepositoryInterface $cookieRepository
     * @param \Extait\Cookie\Api\Data\CategoryRepositoryInterface $categoryRepository
     */
    public function __construct(
        CookieFactory $cookieFactory,
        CategoryFactory $categoryFactory,
        CookieRepositoryInterface $cookieRepository,
        CategoryRepositoryInterface $categoryRepository
    ) {
        $this->cookieFactory = $cookieFactory;
        $this->categoryFactory = $categoryFactory;
        $this->cookieRepository = $cookieRepository;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * Upgrades data for a module.
     *
     * @param \Magento\Framework\Setup\ModuleDataSetupInterface $setup
     * @param \Magento\Framework\Setup\ModuleContextInterface $context
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @return void
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        if (version_compare($context->getVersion(), '1.1.0', '<')) {
            $googleCategory = $this->createGoogleCategory();
            $this->addGoogleCookies($googleCategory);
        }
    }

    /**
     * Create the Google category.
     *
     * @return \Extait\Cookie\Model\Category
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function createGoogleCategory()
    {
        $category = $this->categoryFactory->create();

        $category->setName('Google Analytics');
        $category->setDescription(
            'A set of cookies to collect information and report about website usage statistics ' .
            'without personally identifying individual visitors to Google.'
        );
        $category->setIsSystem(0);

        $this->categoryRepository->save($category);

        return $category;
    }

    /**
     * Add cookies to the google category.
     *
     * @param \Extait\Cookie\Api\Data\CategoryInterface $category
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function addGoogleCookies(CategoryInterface $category)
    {
        $cookies = [
            '_ga' => [
                CookieInterface::DESCRIPTION => 'Used to distinguish users.',
            ],
            '_gid' => [
                CookieInterface::DESCRIPTION => 'Used to distinguish users.',
            ],
            '_gat' => [
                CookieInterface::DESCRIPTION => 'Used to throttle request rate.',
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
}
