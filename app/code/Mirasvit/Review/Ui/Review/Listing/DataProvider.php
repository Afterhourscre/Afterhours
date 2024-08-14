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
 * @package   mirasvit/module-review
 * @version   1.1.2
 * @copyright Copyright (C) 2024 Mirasvit (https://mirasvit.com/)
 */


declare(strict_types=1);


namespace Mirasvit\Review\Ui\Review\Listing;

use Magento\Framework\Api\Search\SearchResultInterface;
use Mirasvit\Review\Model\Review;
use Mirasvit\Review\Api\Data\ReviewInterface;

class DataProvider extends \Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider
{

    private $collection;

    public function getData(): array
    {
        /** @var Collection|SearchResultInterface $collection */
        $collection = $this->getCollection();

        $data = $this->searchResultToOutput($collection);


        return $data;
    }


    public function getCollection()
    {
        if (!$this->collection) {
            $this->collection = $this->getSearchResult();
        }

        return $this->collection;
    }

    protected function searchResultToOutput(SearchResultInterface $searchResult)
    {
        $arrItems          = [];
        $arrItems['items'] = [];

        /** @var Review $reviewItem */
        foreach ($searchResult->getItems() as $reviewItem) {
            $reviewItem->load($reviewItem->getId());

            $reviewData = $reviewItem->getData();

            if ($product = $reviewItem->getProduct()) {
                $reviewData['product_name'] = $product->getName();
                $reviewData['sku']          = $product->getSku();
            }

            $reviewData['stores'] = isset($reviewData['stores']) ? explode(',', $reviewData['stores']) : [];

            $arrItems['items'][] = $reviewData;
        }

        $arrItems['totalRecords'] = $searchResult->getTotalCount();

        return $arrItems;
    }

    public function addFilter(\Magento\Framework\Api\Filter $filter)
    {
        if ($filter->getField() === 'product_name') {
            $collection = $this->getCollection();
            $collection->getSelect()->having("product_name LIKE ?", $filter->getValue());
        } elseif ($filter->getField() === 'created_at') {
            $collection = $this->getCollection();
            $collection->addFieldToFilter('main_table.' . ReviewInterface::CREATED_AT, [$filter->getConditionType() => $filter->getValue()]);
        } elseif ($filter->getField() === 'review_id') {
            $collection = $this->getCollection();
            $collection->addFieldToFilter('main_table.' . ReviewInterface::ID, [$filter->getConditionType() => $filter->getValue()]);
        } else {
            parent::addFilter($filter);
        }


    }

    public function getSearchResult()
    {
        return parent::getSearchResult();
    }

}
