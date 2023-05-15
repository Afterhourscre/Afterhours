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

namespace Extait\Cookie\Model\Source\Options;

use Extait\Cookie\Api\Data\CategoryInterface;
use Extait\Cookie\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use Magento\Framework\Option\ArrayInterface;

class Categories implements ArrayInterface
{
    /**
     * @var \Extait\Cookie\Model\ResourceModel\Category\CollectionFactory
     */
    protected $categoryCollectionFactory;

    /**
     * Categories constructor.
     *
     * @param \Extait\Cookie\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory
     */
    public function __construct(CategoryCollectionFactory $categoryCollectionFactory)
    {
        $this->categoryCollectionFactory = $categoryCollectionFactory;
    }

    /**
     * @inheritDoc
     */
    public function toOptionArray()
    {
        $options = [];

        foreach ($this->toArray() as $value => $label) {
            $options[] = ['value' => $value, 'label' => $label];
        }

        return $options;
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        $array = [];
        $categoryCollection = $this->categoryCollectionFactory->create();

        /** @var CategoryInterface $category */
        foreach ($categoryCollection->getItems() as $category) {
            $array[$category->getId()] = $category->getName();
        }

        return $array;
    }
}
