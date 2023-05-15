<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-seo-filter
 * @version   1.0.16
 * @copyright Copyright (C) 2020 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\SeoFilter\Model;

use Magento\Catalog\Model\ResourceModel\Product as ProductResource;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory as EntityAttributeOptionCollectionFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Registry;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;

class Context
{
    /**
     * @var ProductResource
     */
    private $productResource;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var EntityAttributeOptionCollectionFactory
     */
    private $attributeOptionCollectionFactory;

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * Context constructor.
     * @param ProductResource $productResource
     * @param StoreManagerInterface $storeManager
     * @param EntityAttributeOptionCollectionFactory $entityAttributeOptionCollectionFactory
     * @param UrlInterface $urlBuilder
     * @param Registry $registry
     * @param RequestInterface $request
     */
    public function __construct(
        ProductResource $productResource,
        StoreManagerInterface $storeManager,
        EntityAttributeOptionCollectionFactory $entityAttributeOptionCollectionFactory,
        UrlInterface $urlBuilder,
        Registry $registry,
        RequestInterface $request
    ) {
        $this->productResource                  = $productResource;
        $this->storeManager                     = $storeManager;
        $this->attributeOptionCollectionFactory = $entityAttributeOptionCollectionFactory;
        $this->urlBuilder                       = $urlBuilder;
        $this->registry                         = $registry;
        $this->request                          = $request;
    }

    /**
     * @return int
     */
    public function getStoreId()
    {
        return $this->storeManager->getStore()->getId();
    }

    /**
     * @param int|string $attribute
     *
     * @return \Magento\Catalog\Model\ResourceModel\Eav\Attribute|false
     */
    public function getAttribute($attribute)
    {
        $attr = $this->productResource->getAttribute($attribute);

        return $attr;
    }

    /**
     * @param string|int $attribute
     *
     * @return bool
     */
    public function isDecimalAttribute($attribute)
    {
        return $this->getAttribute($attribute)->getFrontendInput() == 'price';
    }

    /**
     * @param int $attributeId
     * @param int $optionId
     *
     * @return bool|\Magento\Eav\Model\Entity\Attribute\Option
     */
    public function getAttributeOption($attributeId, $optionId)
    {
        /** @var \Magento\Eav\Model\Entity\Attribute\Option $item */
        $item = $this->attributeOptionCollectionFactory->create()
            ->setStoreFilter($this->getStoreId(), true)
            ->setAttributeFilter($attributeId)
            ->setIdFilter($optionId)
            ->getFirstItem();

        return $item->getId() ? $item : false;
    }

    /**
     * @return UrlInterface
     */
    public function getUrlBuilder()
    {
        return $this->urlBuilder;
    }

    /**
     * @return \Magento\Catalog\Model\Category|false
     */
    public function getCurrentCategory()
    {
        return $this->registry->registry('current_category');
    }

    /**
     * @return \Magento\Framework\App\Request\Http
     */
    public function getRequest()
    {
        return $this->request;
    }
}
