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

use Extait\Cookie\Api\Data\CookieInterface;
use Extait\Cookie\Model\ResourceModel\Cookie\CollectionFactory as CookieCollectionFactory;
use Magento\Framework\Option\ArrayInterface;
use Magento\Framework\Registry;

class CookiesNoSystem implements ArrayInterface
{
    /**
     * @var \Extait\Cookie\Model\ResourceModel\Category\CollectionFactory
     */
    protected $cookieCollectionFactory;

    /**
     * @var Registry
     */
    private $registry;

    /**
     * Categories constructor.
     *
     * @param \Extait\Cookie\Model\ResourceModel\Cookie\CollectionFactory $cookieCollectionFactory
     * @param Registry $registry
     */
    public function __construct(
        CookieCollectionFactory $cookieCollectionFactory,
        Registry $registry
    ) {
        $this->cookieCollectionFactory = $cookieCollectionFactory;
        $this->registry = $registry;
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
        $cookieCollection = $this->cookieCollectionFactory->create();

        $category = $this->registry->registry('current_category');
        if ($category && $category->getId()) {
            $cookieCollection->addFieldToFilter([
                CookieInterface::CATEGORY_ID,
                CookieInterface::CATEGORY_ID
            ], [
                ['null' => true],
                ['eq' => $category->getId()]
            ]);
        } else {
            $cookieCollection->addFieldToFilter(CookieInterface::CATEGORY_ID, ['null' => true]);
        }

        /** @var \Extait\Cookie\Api\Data\CookieInterface $cookie */
        foreach ($cookieCollection->getItems() as $cookie) {
            $array[$cookie->getId()] = $cookie->getName();
        }

        return $array;
    }
}
