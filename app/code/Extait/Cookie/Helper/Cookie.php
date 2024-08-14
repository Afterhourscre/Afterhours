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

use Extait\Cookie\Api\Data\CookieInterface;
use Extait\Cookie\Model\ResourceModel\Cookie\CollectionFactory as CookieCollectionFactory;
use Extait\Cookie\Api\Data\CookieRepositoryInterface;
use Extait\Cookie\Model\CookieFactory;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Json\Decoder;
use Magento\Framework\Stdlib\Cookie\PhpCookieManager;
use Magento\Store\Model\ScopeInterface;

class Cookie extends AbstractHelper
{
    /**
     * General config XML paths.
     */
    const CONFIG_XML_PATH_MODULE_IS_ENABLED = 'extait_cookie/general/enable';
    const CONFIG_XML_PATH_SHOW_COOKIE_IN_CATEGORY = 'extait_cookie/general/show_cookie_in_category';
    const CONFIG_XML_PATH_COOKIE_MESSAGE = 'extait_cookie/general/message';

    /**
     * Magento config XML paths.
     */
    const CONFIG_XML_PATH_DEFAULT_COOKIE_IS_ENABLED = 'web/cookie/cookie_restriction';

    /**
     * @var \Extait\Cookie\Model\CookieFactory
     */
    protected $cookieFactory;

    /**
     * @var \Extait\Cookie\Model\ResourceModel\Cookie\CollectionFactory
     */
    protected $cookieCollectionFactory;

    /**
     * @var \Extait\Cookie\Api\Data\CookieRepositoryInterface
     */
    protected $cookieRepository;

    /**
     * @var \Magento\Framework\Stdlib\Cookie\PhpCookieManager
     */
    protected $phpCookieManager;

    /**
     * @var \Magento\Framework\Json\Decoder
     */
    protected $jsonDecoder;

    /**
     * @var array|null
     */
    private $categoriesIDs;

    /**
     * @var array|null
     */
    private $allowedCookies;

    /**
     * @var array|null
     */
    private $disallowedCookies;

    /**
     * @var array|null
     */
    private $allCookiesNames;

    /**
     * Cookie constructor.
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Extait\Cookie\Model\CookieFactory $cookieFactory
     * @param \Extait\Cookie\Model\ResourceModel\Cookie\CollectionFactory $cookieCollectionFactory
     * @param \Extait\Cookie\Api\Data\CookieRepositoryInterface $cookieRepository
     * @param \Magento\Framework\Stdlib\Cookie\PhpCookieManager $phpCookieManager
     * @param \Magento\Framework\Json\Decoder $jsonDecoder
     */
    public function __construct(
        Context $context,
        CookieFactory $cookieFactory,
        CookieCollectionFactory $cookieCollectionFactory,
        CookieRepositoryInterface $cookieRepository,
        PhpCookieManager $phpCookieManager,
        Decoder $jsonDecoder
    ) {
        parent::__construct($context);

        $this->cookieFactory = $cookieFactory;
        $this->cookieCollectionFactory = $cookieCollectionFactory;
        $this->cookieRepository = $cookieRepository;
        $this->phpCookieManager = $phpCookieManager;
        $this->jsonDecoder = $jsonDecoder;
    }

    /**
     * Check whether the module is enabled or not.
     *
     * @return bool
     */
    public function isModuleEnabled()
    {
        return (bool)$this->scopeConfig->getValue(
            self::CONFIG_XML_PATH_MODULE_IS_ENABLED,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return bool
     */
    public function showCookieInCategory()
    {
        return (bool)$this->scopeConfig->getValue(
            self::CONFIG_XML_PATH_SHOW_COOKIE_IN_CATEGORY,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get a cookie restriction message.
     *
     * @return string
     */
    public function getCookieRestrictionMessage()
    {
        return (string)$this->scopeConfig->getValue(
            self::CONFIG_XML_PATH_COOKIE_MESSAGE,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get list of allowed by user cookies names.
     *
     * @return array
     */
    public function getUserAllowedCookiesNames()
    {
        if(!$this->allowedCookies) {
            $allowedCookies = $this->getAllCookiesNames();
            $allowedCategoriesIDs = $this->getUserAllowedCategoriesIDs();

            if (!empty($allowedCategoriesIDs)) {
                $allowedCookies = $this->cookieCollectionFactory->create()
                    ->addFieldToFilter(CookieInterface::CATEGORY_ID, ['in' => $allowedCategoriesIDs])
                    ->getColumnValues(CookieInterface::NAME);
            }

            $this->allowedCookies = $allowedCookies;
        }

        return $this->allowedCookies;
    }

    /**
     * Get list of disallowed by user cookies names.
     *
     * @return array
     */
    public function getUserDisallowedCookiesNames()
    {
        if(!$this->disallowedCookies) {
            $disallowedCookies = [];
            $allowedCategoriesIDs = $this->getUserAllowedCategoriesIDs();

            if (!empty($allowedCategoriesIDs)) {
                $disallowedCookies = $this->cookieCollectionFactory->create()
                    ->addFieldToFilter(CookieInterface::CATEGORY_ID, ['nin' => $allowedCategoriesIDs])
                    ->getColumnValues(CookieInterface::NAME);
            }

            $this->disallowedCookies = $disallowedCookies;
        }


        return $this->disallowedCookies;
    }

    /**
     * Get user allowed categories IDs.
     *
     * @return array
     */
    public function getUserAllowedCategoriesIDs()
    {
        if(!$this->categoriesIDs) {
            $categoriesIDs = [];
            $cookieValue = $this->phpCookieManager->getCookie('extait_allowed_categories');

            if ($this->phpCookieManager->getCookie('extait_allowed_categories') !== null) {
                $categoriesIDs = $this->jsonDecoder->decode($cookieValue);
            }

            $this->categoriesIDs = $categoriesIDs;
        }


        return $this->categoriesIDs;
    }

    /**
     * Get all Cookies entities names.
     *
     * @return array
     */
    public function getAllCookiesNames()
    {
        if(!$this->allCookiesNames) {
            $this->allCookiesNames = $this->cookieCollectionFactory->create()->getColumnValues(CookieInterface::NAME);
        }

        return $this->allCookiesNames;
    }

    /**
     * Create an empty cookie entity.
     *
     * @param string $name
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function createEmptyCookie($name)
    {
        $cookie = $this->cookieFactory->create();
        $cookie->setName($name);

        $this->cookieRepository->save($cookie);
    }
}
