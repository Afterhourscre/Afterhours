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

namespace Extait\Cookie\Block\Settings;

use Extait\Cookie\Api\Data\CategoryRepositoryInterface;
use Extait\Cookie\Helper\CategoryManager;
use Extait\Cookie\Helper\Cookie;
use Magento\Framework\View\Element\Template;
use Magento\Framework\Api\SearchCriteriaBuilder;

/** @api */
class Index extends Template
{
    /**
     * @var \Extait\Cookie\Api\Data\CategoryRepositoryInterface
     */
    protected $categoryRepository;
    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var CategoryManager
     */
    private $categoryManager;

    /**
     * @var Cookie
     */
    private $cookieHelper;

    /**
     * Index constructor.
     *
     * @param Template\Context $context
     * @param \Extait\Cookie\Api\Data\CategoryRepositoryInterface $categoryRepository
     * @param CategoryManager $categoryManager
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param Cookie $cookieHelper
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        CategoryRepositoryInterface $categoryRepository,
        CategoryManager $categoryManager,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        Cookie $cookieHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->categoryRepository = $categoryRepository;
        $this->categoryManager = $categoryManager;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->cookieHelper = $cookieHelper;
    }

    /**
     * Get all cookie categories.
     *
     * @return \Extait\Cookie\Api\Data\CategorySearchResultsInterface
     */
    public function getCookieCategories()
    {
        $currentStore = $this->_storeManager->getStore();

        return $this->categoryRepository->getList($this->searchCriteriaBuilder->create(), $currentStore->getId());
    }

    /**
     * Get user selected categories.
     *
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getUserSelectedCategories()
    {
        return $this->categoryManager->getUserAllowedCategoriesIDs();
    }

    /**
     * Get the form action.
     *
     * @return string
     */
    public function getFormAction()
    {
        return $this->getUrl('cookie/settings/save');
    }

    /**
     * @return bool
     */
    public function showCookieInCategory()
    {
        return $this->cookieHelper->showCookieInCategory();
    }
}
