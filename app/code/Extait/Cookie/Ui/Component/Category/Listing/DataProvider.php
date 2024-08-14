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

namespace Extait\Cookie\Ui\Component\Category\Listing;

use Extait\Cookie\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use Magento\Ui\DataProvider\AbstractDataProvider;

class DataProvider extends AbstractDataProvider
{
    /**
     * DataProvider constructor.
     *
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param \Extait\Cookie\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CategoryCollectionFactory $categoryCollectionFactory,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);

        $this->collection = $categoryCollectionFactory->create();
    }

    /**
     * @inheritdoc
     */
    public function getData()
    {
        $col = $this->getCollection();

        $col->getSelect()->joinLeft(
            ['eccs' => 'extait_cookie_category_store'],
            'main_table.id = eccs.category_id',
            ['name', 'description']
        )->where('eccs.store_id = ?', 0);

        return $col->toArray();
    }
}
