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

namespace Extait\Cookie\Controller\Adminhtml;

use Extait\Cookie\Helper\Cookie as CookieHelper;
use Extait\Cookie\Api\Data\CategoryRepositoryInterface;
use Extait\Cookie\Model\CategoryFactory;
use Extait\Cookie\Model\ResourceModel\Category as CategoryResourceModel;
use Extait\Cookie\Model\CookieFactory;
use Extait\Cookie\Model\ResourceModel\Cookie as CookieResourceModel;
use Extait\Cookie\Model\ResourceModel\Cookie\CollectionFactory as CookieCollectionFactory;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;

abstract class AbstractController extends Action
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Extait\Cookie\Helper\Cookie
     */
    protected $cookieHelper;

    /**
     * @var \Extait\Cookie\Model\CategoryFactory
     */
    protected $categoryFactory;

    /**
     * @var \Extait\Cookie\Model\ResourceModel\Cookie
     */
    protected $categoryResourceModel;

    /**
     * @var \Extait\Cookie\Model\CookieFactory
     */
    protected $cookieFactory;

    /**
     * @var \Extait\Cookie\Model\ResourceModel\Cookie
     */
    protected $cookieResourceModel;

    /**
     * @var \Extait\Cookie\Api\Data\CategoryRepositoryInterface
     */
    protected $categoryRepository;

    /**
     * @var CookieCollectionFactory
     */
    protected $cookieCollectionFactory;

    /**
     * Delete constructor.
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Extait\Cookie\Helper\Cookie $cookieHelper
     * @param \Extait\Cookie\Model\CategoryFactory $categoryFactory
     * @param \Extait\Cookie\Model\ResourceModel\Category $categoryResourceModel
     * @param \Extait\Cookie\Model\CookieFactory $cookieFactory
     * @param \Extait\Cookie\Model\ResourceModel\Cookie $cookieResourceModel
     * @param CookieCollectionFactory $cookieCollectionFactory
     * @param \Extait\Cookie\Api\Data\CategoryRepositoryInterface $categoryRepository
     */
    public function __construct(
        Context $context,
        Registry $registry,
        CookieHelper $cookieHelper,
        CategoryFactory $categoryFactory,
        CategoryResourceModel $categoryResourceModel,
        CookieFactory $cookieFactory,
        CookieResourceModel $cookieResourceModel,
        CookieCollectionFactory $cookieCollectionFactory,
        CategoryRepositoryInterface $categoryRepository
    ) {
        parent::__construct($context);

        $this->registry = $registry;
        $this->cookieHelper = $cookieHelper;
        $this->categoryFactory = $categoryFactory;
        $this->categoryResourceModel = $categoryResourceModel;
        $this->cookieFactory = $cookieFactory;
        $this->cookieResourceModel = $cookieResourceModel;
        $this->categoryRepository = $categoryRepository;
        $this->cookieCollectionFactory = $cookieCollectionFactory;
    }
}
