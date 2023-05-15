<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_CallForPrice
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\CallForPrice\Helper;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\ProductFactory;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\View\Asset\Repository as AssetFile;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Mageplaza\CallForPrice\Model\Config\Source\AttributeOptions;
use Mageplaza\CallForPrice\Model\ResourceModel\Requests\CollectionFactory as RequestsCollection;
use Mageplaza\CallForPrice\Model\ResourceModel\Rules\CollectionFactory as RulesCollection;
use Mageplaza\CallForPrice\Model\RulesFactory;
use Mageplaza\CallForPrice\Model\Status;

/**
 * Class Rule
 * @package Mageplaza\CallForPrice\Helper
 */
class Rule extends Data
{
    const MPCALLFORPRICE_ATTRIBUTE_CODE = 'mp_callforprice';

    /**
     * @var CustomerSession
     */
    protected $_customerSession;

    /**
     * @var HttpContext
     */
    protected $_httpContext;

    /**
     * @var AssetFile
     */
    protected $_assetRepo;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $_request;

    /**
     * @var ProductRepository
     */
    protected $_productRepository;

    /**
     * @var RulesFactory
     */
    protected $_rulesFactory;

    /**
     * @var RulesCollection
     */
    protected $_rulesCollection;

    /**
     * @var bool|Rule
     */
    protected $mpCallForPriceRule;

    /**
     * @var DateTime
     */
    protected $_datetime;

    /**
     * @var RulesFactory
     */
    protected $_productFactory;

    /**
     * @var TransportBuilder
     */
    protected $transportBuilder;

    /**
     * @var RequestsCollection
     */
    protected $_requestsCollection;

    /**
     * Rule constructor.
     *
     * @param Context $context
     * @param ObjectManagerInterface $objectManager
     * @param StoreManagerInterface $storeManager
     * @param CustomerSession $customerSession
     * @param HttpContext $httpcontext
     * @param AssetFile $assetRepo
     * @param Http $request
     * @param TransportBuilder $transportBuilder
     * @param ProductRepositoryInterface $productRepository
     * @param RulesFactory $rulesFactory
     * @param RulesCollection $rulesCollection
     * @param DateTime $dateTime
     * @param ProductFactory $productFactory
     * @param RequestsCollection $requestsCollection
     */
    public function __construct(
        Context $context,
        ObjectManagerInterface $objectManager,
        StoreManagerInterface $storeManager,
        CustomerSession $customerSession,
        HttpContext $httpcontext,
        AssetFile $assetRepo,
        Http $request,
        TransportBuilder $transportBuilder,
        ProductRepositoryInterface $productRepository,
        RulesFactory $rulesFactory,
        RulesCollection $rulesCollection,
        DateTime $dateTime,
        ProductFactory $productFactory,
        RequestsCollection $requestsCollection
    )
    {
        $this->_productRepository  = $productRepository;
        $this->_rulesFactory       = $rulesFactory;
        $this->_rulesCollection    = $rulesCollection;
        $this->_datetime           = $dateTime;
        $this->_productFactory     = $productFactory;
        $this->_requestsCollection = $requestsCollection;

        parent::__construct($context, $objectManager, $storeManager, $customerSession, $httpcontext, $assetRepo, $request, $transportBuilder);
    }

    /**
     * @param $productId
     *
     * @return \Magento\Catalog\Api\Data\ProductInterface|mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getProductById($productId)
    {
        return $product = $this->_productRepository->getById($productId);
    }

    /**
     * @param null $storeId
     *
     * @return mixed
     */
    public function getRulesCollection($storeId = null)
    {
        if (is_null($storeId)) {
            $storeId = $this->storeManager->getStore()->getId();
        }

        $collection = $this->_rulesCollection->create()
            ->addFieldToFilter('status', Status::ENABLED)
            ->addFieldToFilter('store_ids', [
                ['finset' => Store::DEFAULT_STORE_ID],
                ['finset' => $storeId]
            ])
            ->setOrder('priority', 'asc');

        return $collection;
    }

    /**
     * @param $rule_id
     *
     * @return \Mageplaza\CallForPrice\Model\Rules
     */
    public function getRulesById($rule_id)
    {
        return $this->_rulesFactory->create()->load($rule_id);
    }

    /**
     * Check expire date
     *
     * @param $rule_id
     *
     * @return bool
     */
    public function checkExpireDate($rule_id)
    {
        $now        = $this->_datetime->gmtDate();
        $rulesModel = $this->getRulesById($rule_id);
        $fromDate   = $rulesModel->getCreatedAt();
        $toDate     = $rulesModel->getToDate();

        /** input now > to_date*/
        if (strtotime($now) < strtotime($fromDate)) {
            return false;
        }

        /** input from_date > to_date*/
        if ($toDate != '' && strtotime($toDate) < strtotime($fromDate)) {
            return false;
        }

        if (!$toDate) {
            return true;
        }

        return (strtotime($now) <= strtotime($toDate));
    }

    /**
     * @param $rule_id
     *
     * @return bool
     */
    public function checkCustomerGroup($rule_id)
    {
        $rulesModel               = $this->getRulesById($rule_id);
        $customerGroupChooseArray = explode(',', $rulesModel->getCustomerGroupIds());

        $customerGroupId = 0;
        if ($this->getCustomerLogedIn()) {
            $customerGroupId = $this->getCustomerGroupId();
        }

        return in_array($customerGroupId, $customerGroupChooseArray);
    }

    /**
     * @param $productById
     *
     * @return bool|int|Rule
     */
    public function validateProductInRuleAvailable($productById)
    {
        if (!$this->isEnabled()) {
            return false;
        }
        $this->mpCallForPriceRule = false;

        $product            = $this->_productFactory->create()->load($productById);
        $attributeCfpValues = $product->getCustomAttribute('mp_callforprice');
        $attributeCfpValues = $attributeCfpValues ? $attributeCfpValues->getValue() : AttributeOptions::ATTRIBUTE_PARENT_CATEGORY;

        /** if the product not accept rules */
        if ($attributeCfpValues == 0) {
            return $this->mpCallForPriceRule;
        }
        /** if the product use the current rules */
        if ($attributeCfpValues == -1) {
            $ruleCollection = $this->getRulesCollection();
            foreach ($ruleCollection as $rule) {
                if ($rule->getConditions()->validate($product)) {
                    if ($this->checkExpireDate($rule->getRuleId()) && $this->checkCustomerGroup($rule->getRuleId())) {
                        $this->mpCallForPriceRule = $rule;
                        break;
                    }
                }
            }
        } else {
            if ($this->checkExpireDate($attributeCfpValues) && $this->checkCustomerGroup($attributeCfpValues)) {
                $ruleChoosed = $this->_rulesFactory->create()->load($attributeCfpValues);
                if ($ruleChoosed->getStatus() == Status::ENABLED) {
                    $this->mpCallForPriceRule = $ruleChoosed;
                }
            }
        }

        return $this->mpCallForPriceRule;
    }

    /**
     * @param $productId
     *
     * @return int
     */
    public function getRankRequests($productId)
    {
        $collection = $this->_requestsCollection->create()->addFieldToFilter('product_id', $productId);

        return count($collection) + 1;
    }
}
