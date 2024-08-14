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

namespace Mageplaza\CallForPrice\Model;

use Magento\Backend\Model\Session;
use Magento\Catalog\Model\ProductFactory;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Model\ResourceModel\Iterator;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Rule\Model\AbstractModel;
use Mageplaza\CallForPrice\Helper\Data as HelperData;

/**
 * Class Rules
 * @package Mageplaza\CallForPrice\Model
 */
class Rules extends AbstractModel
{
    /**
     * Cache tag
     *
     * @var string
     */
    const CACHE_TAG = 'mageplaza_callforprice_rule';

    /**
     * Cache tag
     *
     * @var string
     */
    protected $_cacheTag = 'mageplaza_callforprice_rule';

    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'mageplaza_callforprice_rule';

    /**
     * @var string
     */
    protected $_idFieldName = 'rule_id';

    /**
     * Store matched product Ids
     *
     * @var array
     */
    protected $_productIds;

    /**
     * Store matched product Ids with rule id
     *
     * @var array
     */
    protected $dataProductIds;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productFactory;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * @var \Magento\Framework\Model\ResourceModel\Iterator
     */
    protected $resourceIterator;

    /**
     * @var Session
     */
    protected $_backendSession;

    /**
     * @var RequestInterface
     */
    protected $_request;

    /**
     * @var HelperData
     */
    protected $_helperData;

    /**
     * Rules constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param TimezoneInterface $localeDate
     * @param ProductFactory $productFactory
     * @param CollectionFactory $productCollectionFactory
     * @param RequestInterface $request
     * @param HelperData $helperData
     * @param Session $backendSession
     * @param Iterator $resourceIterator
     * @param AbstractDb|null $resourceCollection
     * @param AbstractResource|null $resource
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        TimezoneInterface $localeDate,
        ProductFactory $productFactory,
        CollectionFactory $productCollectionFactory,
        RequestInterface $request,
        HelperData $helperData,
        Session $backendSession,
        Iterator $resourceIterator,
        AbstractDb $resourceCollection = null,
        AbstractResource $resource = null
    )
    {
        $this->productFactory           = $productFactory;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->resourceIterator         = $resourceIterator;
        $this->_backendSession          = $backendSession;
        $this->_request                 = $request;
        $this->_helperData              = $helperData;

        parent::__construct($context, $registry, $formFactory, $localeDate, $resource, $resourceCollection);
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Mageplaza\CallForPrice\Model\ResourceModel\Rules');
    }

    /**
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * Get rule condition combine model instance
     *
     * @return \Magento\Rule\Model\Condition\Combine
     */
    public function getConditionsInstance()
    {
        return $this->getActionsInstance();
    }

    /**
     * @return \Magento\CatalogRule\Model\Rule\Condition\Combine|\Magento\Rule\Model\Action\Collection
     */
    public function getActionsInstance()
    {
        return ObjectManager::getInstance()->create(\Magento\CatalogRule\Model\Rule\Condition\Combine::class);
    }

    /**
     * @return array|null
     * @throws \Zend_Serializer_Exception
     */
    public function getMatchingProductIds()
    {
        if ($this->_productIds === null) {
            $data = $this->_request->getPost('rule');
            /** Fix filter grid error */
            if ($data) {
                $this->_backendSession->setProductLabelsData(['rule' => $data]);
            } else {
                $productLabelsData = $this->_backendSession->getProductLabelsData();
                $data              = $productLabelsData['rule'];
            }

            if (!$data) {
                $data = [];
            }

            $this->_productIds = [];
            $this->setCollectedAttributes([]);

            /** @var $productCollection \Magento\Catalog\Model\ResourceModel\Product\Collection */
            $productCollection = $this->productCollectionFactory->create();
            $productCollection
                ->addAttributeToSelect('*')
                ->addAttributeToFilter('visibility', \Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH)
                ->addAttributeToFilter('status', \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED);

            $this->loadPost($data);
            $this->setConditionsSerialized($this->_helperData->serialize($this->getConditions()->asArray()));
            $this->getConditions()->collectValidatedAttributes($productCollection);

            $this->resourceIterator->walk(
                $productCollection->getSelect(),
                [[$this, 'callbackValidateProduct']],
                [
                    'attributes' => $this->getCollectedAttributes(),
                    'product'    => $this->productFactory->create(),
                ]
            );
        }

        return $this->_productIds;
    }

    /**
     * Callback function for product matching
     *
     * @param $args
     */
    public function callbackValidateProduct($args)
    {
        $product = clone $args['product'];
        $product->setData($args['row']);

        if ($this->getConditions()->validate($product)) {
            $this->_productIds[] = $product->getId();
        }
    }
}
