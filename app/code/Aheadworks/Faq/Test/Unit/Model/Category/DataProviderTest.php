<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */


namespace Aheadworks\Faq\Test\Unit\Model\Category;

use Aheadworks\Faq\Model\Category;
use Aheadworks\Faq\Model\Category\DataProvider;
use Aheadworks\Faq\Model\ResourceModel\Category\Collection;
use Aheadworks\Faq\Model\ResourceModel\Category\CollectionFactory;
use Aheadworks\Faq\Model\Url;
use Magento\Framework\App\Request\DataPersistor;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;

/**
 * Test for DataProvider
 *
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class DataProviderTest extends TestCase
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var CollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $collectionFactoryMock;

    /**
     * @var DataPersistor|\PHPUnit_Framework_MockObject_MockObject
     */
    private $dataPersistorMock;

    /**
     * @var Url|\PHPUnit_Framework_MockObject_MockObject
     */
    private $urlMock;

    /**
     * @var Collection|\PHPUnit_Framework_MockObject_MockObject
     */
    private $collectionMock;

    /**
     * @var array
     */
    private $categoryMocks;

    /**
     * @var DataProvider
     */
    private $dataProviderObject;

    /**
     * Initialize model
     */
    public function setUp()
    {
        $this->objectManager = new ObjectManager($this);

        $this->collectionFactoryMock = $this->createMock(CollectionFactory::class);
        $this->dataPersistorMock = $this->createMock(DataPersistor::class);
        $this->urlMock = $this->createMock(Url::class);
        $this->collectionMock = $this->objectManager->getCollectionMock(Collection::class, []);

        $this->collectionFactoryMock
            ->expects($this->once())
            ->method('create')
            ->willReturn($this->collectionMock);

        $this->dataProviderObject = $this->objectManager->getObject(
            DataProvider::class,
            [
                'name' => 'name',
                'primaryFieldName' => 'primary_field_name',
                'requestFieldName' => 'request_field_name',
                'categoryCollectionFactory' => $this->collectionFactoryMock,
                'dataPersistor' => $this->dataPersistorMock,
                'url' => $this->urlMock,
                'meta' => ['meta'],
                'data' => []
            ]
        );

        $this->categoryMocks = [];
    }

    /**
     * Prepares Meta
     *
     * @covers DataProvider::prepareMeta
     */
    public function testPrepareMeta()
    {
        $meta = ['field' => 'value'];

        $this->assertEquals($meta, $this->dataProviderObject->prepareMeta($meta));
    }

    /**
     * Prepare category mocks
     *
     * @param bool $categoryIconExist
     * @param bool $articleListIconExist
     *
     * @return array
     */
    private function generateCategoryMocks($categoryIconExist = true, $articleListIconExist = true)
    {
        $expected = [];

        $urlMockCalls = 0;

        for ($i = 0; $i < 3; $i++) {
            $data = ['category_id' => $i, 'votes_yes' => $i, 'votes_no' => $i + 1];

            if ($categoryIconExist) {
                $data = array_merge($data, ['category_icon' => 'category_icon-' . $i]);
            }

            if ($articleListIconExist) {
                $data = array_merge($data, ['article_list_icon' => 'article_list_icon-' . $i]);
            }

            $categoryMock = $this->createMock(Category::class);

            $categoryMock
                ->expects($this->once())
                ->method('getData')
                ->willReturn($data);

            $categoryMock
                ->expects($this->any())
                ->method('getCategoryId')
                ->willReturn($data['category_id']);

            if ($categoryIconExist) {
                $url = 'http://example.com/path/to/' . $data['category_icon'];

                $categoryMock
                    ->expects($this->once())
                    ->method('getCategoryIcon')
                    ->willReturn($data['category_icon']);

                $this->urlMock
                    ->expects($this->at($urlMockCalls++))
                    ->method('getCategoryIconUrl')
                    ->with($categoryMock)
                    ->willReturn($url);

                $data['category_icon'] = [0 => ['name' => $data['category_icon'], 'url' => $url]];
            } else {
                $categoryMock
                    ->expects($this->never())
                    ->method('getCategoryIcon');

                $this->urlMock
                    ->expects($this->never())
                    ->method('getCategoryIconUrl');
            }

            if ($articleListIconExist) {
                $url = 'http://example.com/path/to/' . $data['article_list_icon'];

                $categoryMock
                    ->expects($this->once())
                    ->method('getArticleListIcon')
                    ->willReturn($data['article_list_icon']);

                $this->urlMock
                    ->expects($this->at($urlMockCalls++))
                    ->method('getArticleListIconUrl')
                    ->with($categoryMock)
                    ->willReturn($url);

                $data['article_list_icon'] = [0 => ['name' => $data['article_list_icon'], 'url' => $url]
                ];
            } else {
                $categoryMock
                    ->expects($this->never())
                    ->method('getArticleListIcon');

                $this->urlMock
                    ->expects($this->never())
                    ->method('getArticleListIconUrl');
            }

            $this->categoryMocks[] = $categoryMock;

            $expected[] = $data;
        }

        return $expected;
    }

    /**
     * Get data
     *
     * @covers DataProvider::getData
     */
    public function testGetData()
    {
        $categoryMocks = $this->generateCategoryMocks(false, false);

        $this->collectionMock
            ->expects($this->once())
            ->method('getItems')
            ->willReturn($this->categoryMocks);

        $this->dataPersistorMock
            ->expects($this->once())
            ->method('get')
            ->with('faq_category')
            ->willReturn([]);

        $this->assertEquals($categoryMocks, $this->dataProviderObject->getData());
        $this->assertEquals($categoryMocks, $this->dataProviderObject->getData());
    }

    /**
     * Get data
     * Category have not article list icon
     *
     * @covers  DataProvider::getData
     * @depends testGetData
     */
    public function testGetDataWithoutArticleListIcon()
    {
        $categoryMocks = $this->generateCategoryMocks(false, false);

        $this->collectionMock
            ->expects($this->once())
            ->method('getItems')
            ->willReturn($this->categoryMocks);

        $this->dataPersistorMock
            ->expects($this->once())
            ->method('get')
            ->with('faq_category')
            ->willReturn([]);

        $this->assertEquals($categoryMocks, $this->dataProviderObject->getData());
        $this->assertEquals($categoryMocks, $this->dataProviderObject->getData());
    }

    /**
     * Get data
     * Category have not icon
     *
     * @covers  DataProvider::getData
     * @depends testGetData
     */
    public function testGetDataWithoutCategoryIcon()
    {
        $categoryMocks = $this->generateCategoryMocks(false, false);

        $this->collectionMock
            ->expects($this->once())
            ->method('getItems')
            ->willReturn($this->categoryMocks);

        $this->dataPersistorMock
            ->expects($this->once())
            ->method('get')
            ->with('faq_category')
            ->willReturn([]);

        $this->assertEquals($categoryMocks, $this->dataProviderObject->getData());
        $this->assertEquals($categoryMocks, $this->dataProviderObject->getData());
    }

    /**
     * Get data
     * Category have not icon and not article list icon
     *
     * @covers  DataProvider::getData
     * @depends testGetData
     */
    public function testGetDataWithoutCategoryIconAndArticleListIcon()
    {
        $categoryMocks = $this->generateCategoryMocks(false, false);

        $this->collectionMock
            ->expects($this->once())
            ->method('getItems')
            ->willReturn($this->categoryMocks);

        $this->dataPersistorMock
            ->expects($this->once())
            ->method('get')
            ->with('faq_category')
            ->willReturn([]);

        $this->assertEquals($categoryMocks, $this->dataProviderObject->getData());
        $this->assertEquals($categoryMocks, $this->dataProviderObject->getData());
    }

    /**
     * Get data. Data persistor contain category
     *
     * @covers  DataProvider::getData
     * @depends testGetData
     */
    public function testGetDataWithCategoryInDataPersistor()
    {
        $categoryMocks = $this->generateCategoryMocks(false, false);

        $data = [
            'category_id' => sizeof($categoryMocks) + 1,
            'category_icon' => [0 => ['name' => 'name', 'url' => 'url']],
            'article_list_icon' => [0 => ['name' => 'name', 'url' => 'url']]
        ];

        $this->collectionMock
            ->expects($this->once())
            ->method('getItems')
            ->willReturn($this->categoryMocks);

        $this->dataPersistorMock
            ->expects($this->once())
            ->method('get')
            ->with('faq_category')
            ->willReturn($data);

        $categoryMock = $this->createMock(Category::class);

        $categoryMock
            ->expects($this->once())
            ->method('setData')
            ->with($data)
            ->willReturnSelf();

        $categoryMock
            ->expects($this->once())
            ->method('getCategoryId')
            ->willReturn($data['category_id']);

        $categoryMock
            ->expects($this->once())
            ->method('getData')
            ->willReturn($data);

        $this->collectionMock
            ->expects($this->once())
            ->method('getNewEmptyItem')
            ->willReturn($categoryMock);

        $this->dataPersistorMock
            ->expects($this->once())
            ->method('clear')
            ->with('faq_category');

        $categoryMocks[$data['category_id']] = $data;

        $this->assertEquals($categoryMocks, $this->dataProviderObject->getData());
    }
}
