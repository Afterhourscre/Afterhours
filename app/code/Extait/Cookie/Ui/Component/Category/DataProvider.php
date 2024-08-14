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

namespace Extait\Cookie\Ui\Component\Category;

use Extait\Cookie\Api\Data\CookieInterface;
use Extait\Cookie\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use Extait\Cookie\Model\ResourceModel\Cookie\CollectionFactory as CookieCollectionFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;

class DataProvider extends AbstractDataProvider
{
    /**
     * @var array
     */
    protected $loadedData;

    /**
     * @var CookieCollectionFactory
     */
    protected $cookieCollectionFactory;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * DataProvider constructor.
     *
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param \Extait\Cookie\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory
     * @param CookieCollectionFactory $cookieCollectionFactory
     * @param \Magento\Framework\App\RequestInterface $request
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CategoryCollectionFactory $categoryCollectionFactory,
        CookieCollectionFactory $cookieCollectionFactory,
        RequestInterface $request,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);

        $this->collection = $categoryCollectionFactory->create();
        $this->cookieCollectionFactory = $cookieCollectionFactory;
        $this->request = $request;
    }

    /**
     * @inheritdoc
     */
    public function getData()
    {
        if (!isset($this->loadedData)) {
            $storeID = $this->request->getParam('store', 0);

            /** @var \Extait\Cookie\Model\ResourceModel\Category\Collection $collection */
            $collection = $this->getCollection();
            $collection->setStoreID($storeID);

            /** @var \Extait\Cookie\Model\Category|\Magento\Framework\DataObject $category */
            foreach ($collection->getItems() as $category) {
                $cookieCollection = $this->cookieCollectionFactory->create();
                $cookieCollection->addFieldToFilter(CookieInterface::CATEGORY_ID, ['eq' => $category->getId()]);

                $data = $category->getData();
                $data['cookies_ids'] = $cookieCollection->getAllIds();
                $this->loadedData[$category->getId()]['category_details'] = $data;
                $this->loadedData[$category->getId()]['disable_fields'] = $category->getIsSystem();
                $this->loadedData[$category->getId()]['store'] = $storeID;
            }
        }

        return $this->loadedData;
    }
}
