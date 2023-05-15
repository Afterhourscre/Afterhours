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

namespace Extait\Cookie\Ui\Component\Cookie;

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
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * DataProvider constructor.
     *
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param \Extait\Cookie\Model\ResourceModel\Cookie\CollectionFactory $cookieCollectionFactory
     * @param \Magento\Framework\App\RequestInterface $request
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CookieCollectionFactory $cookieCollectionFactory,
        RequestInterface $request,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);

        $this->collection = $cookieCollectionFactory->create();
        $this->request = $request;
    }

    /**
     * @inheritdoc
     */
    public function getData()
    {
        if (!isset($this->loadedData)) {
            $storeID = $this->request->getParam('store', 0);

            /** @var \Extait\Cookie\Model\ResourceModel\Cookie\Collection $collection */
            $collection = $this->getCollection();
            $collection->setStoreID($storeID);

            /** @var \Extait\Cookie\Api\Data\CookieInterface|\Magento\Framework\DataObject $cookie */
            foreach ($collection->getItems() as $cookie) {
                $this->loadedData[$cookie->getId()]['cookie_details'] = $cookie->getData();
                $this->loadedData[$cookie->getId()]['disable_fields'] = true;
                $this->loadedData[$cookie->getId()]['store'] = $storeID;
            }
        }

        return $this->loadedData;
    }
}
