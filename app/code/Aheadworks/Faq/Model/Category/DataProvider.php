<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */


namespace Aheadworks\Faq\Model\Category;

use Aheadworks\Faq\Model\ResourceModel\Category\CollectionFactory;
use Aheadworks\Faq\Model\ResourceModel\Category\Collection;
use Aheadworks\Faq\Model\Url;
use Aheadworks\Faq\Model\Category;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;

class DataProvider extends AbstractDataProvider
{
    /**
     * @var Url
     */
    private $url;
    
    /**
     * @var Collection
     */
    protected $collection;

    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @var array
     */
    private $loadedData;

    /**
     * @var FileInfo
     */
    private $fileInfo;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $categoryCollectionFactory
     * @param DataPersistorInterface $dataPersistor
     * @param Url $url
     * @param FileInfo $fileInfo
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $categoryCollectionFactory,
        DataPersistorInterface $dataPersistor,
        Url $url,
        FileInfo $fileInfo,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $categoryCollectionFactory->create();
        $this->dataPersistor = $dataPersistor;
        $this->meta = $this->prepareMeta($this->meta);
        $this->url = $url;
        $this->fileInfo = $fileInfo;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * Prepares Meta
     *
     * @param array $meta
     * @return array
     */
    public function prepareMeta(array $meta)
    {
        return $meta;
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }
        $items = $this->collection->getItems();

        /** @var \Aheadworks\Faq\Api\Data\CategoryInterface|\Aheadworks\Faq\Model\Category $category */
        foreach ($items as $category) {
            $this->loadedData[$category->getCategoryId()] = $this->prepareFormDataForCategory($category);
        }

        $data = $this->dataPersistor->get('faq_category');
        if (!empty($data)) {
            $category = $this->collection->getNewEmptyItem();
            $category->setData($data);
            $this->loadedData[$category->getCategoryId()] = $category->getData();
            $this->dataPersistor->clear('faq_category');
        }

        return $this->loadedData;
    }

    /**
     * Prepare data for category
     *
     * @param Category $category
     * @return mixed
     */
    private function prepareFormDataForCategory($category)
    {
        $data = $category->getData();

        if (isset($data['category_icon'])) {
            $imageName = $category->getCategoryIcon();
            unset($data['category_icon']);
            if ($this->fileInfo->isExist($imageName)) {
                $stat = $this->fileInfo->getStat($imageName);
                $data['category_icon'] = [
                    [
                        'name' => $imageName,
                        'url' => $this->url->getCategoryIconUrl($category),
                        'size' => isset($stat) ? $stat['size'] : 0,
                        'type' => $this->fileInfo->getMimeType($imageName)
                    ]
                ];
            }
        }

        if (isset($data['article_list_icon'])) {
            $imageName = $category->getArticleListIcon();
            unset($data['article_list_icon']);
            if ($this->fileInfo->isExist($imageName)) {
                $stat = $this->fileInfo->getStat($imageName);
                $data['article_list_icon'] = [
                    [
                        'name' => $imageName,
                        'url' => $this->url->getArticleListIconUrl($category),
                        'size' => isset($stat) ? $stat['size'] : 0,
                        'type' => $this->fileInfo->getMimeType($imageName)
                    ]
                ];
            }
        }
        return $data;
    }
}
