<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Test\Unit\Model\Label\Block\Rule;

use Aheadworks\OnSale\Api\Data\LabelInterface;
use Aheadworks\OnSale\Api\Data\LabelSearchResultsInterface;
use Aheadworks\OnSale\Model\Config;
use Aheadworks\OnSale\Model\Label\Block\Rule\Loader;
use Magento\Catalog\Model\Product;
use Magento\Framework\Api\SearchCriteria;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\OnSale\Api\LabelRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Aheadworks\OnSale\Model\ResourceModel\Rule as RuleResource;
use Magento\Framework\Stdlib\DateTime as StdlibDateTime;
use Aheadworks\OnSale\Model\ResourceModel\Rule\Indexer\RuleProduct\RuleProductInterface;

/**
 * Class LoaderTest
 *
 * @package Aheadworks\OnSale\Test\Unit\Model\Label\Block\Rule
 */
class LoaderTest extends TestCase
{
    /**
     * @var Loader
     */
    private $model;

    /**
     * @var SearchCriteriaBuilder|\PHPUnit_Framework_MockObject_MockObject
     */
    private $searchCriteriaBuilderMock;

    /**
     * @var LabelRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $labelRepositoryMock;

    /**
     * @var RuleResource|\PHPUnit_Framework_MockObject_MockObject
     */
    private $ruleResourceMock;

    /**
     * @var DateTime|\PHPUnit_Framework_MockObject_MockObject
     */
    private $dateTimeMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->searchCriteriaBuilderMock = $this->createPartialMock(
            SearchCriteriaBuilder::class,
            ['addFilter', 'create']
        );
        $this->labelRepositoryMock = $this->getMockForAbstractClass(LabelRepositoryInterface::class);
        $this->ruleResourceMock = $this->createPartialMock(RuleResource::class, ['getSortedRulesDataForProduct']);
        $this->dateTimeMock = $this->createPartialMock(DateTime::class, ['gmtDate']);

        $this->model = $objectManager->getObject(
            Loader::class,
            [
                'searchCriteriaBuilder' => $this->searchCriteriaBuilderMock,
                'labelRepository' => $this->labelRepositoryMock,
                'ruleResource' => $this->ruleResourceMock,
                'dateTime' => $this->dateTimeMock
            ]
        );
    }

    /**
     * Test getAvailableRulesForProduct method
     */
    public function testGetAvailableRulesForProduct()
    {
        $expected = [
            [RuleProductInterface::LABEL_ID => 1, RuleProductInterface::LABEL_TEXT_LARGE => 'text']
        ];
        $customerGroupId = 1;
        $currentDate = '2018-06-01 00:00:00';
        $productData = ['id' => 1, 'store_id' => 1];

        $productMock = $this->createPartialMock(Product::class, ['getId', 'getStoreId']);
        $productMock->expects($this->once())
            ->method('getId')
            ->willReturn($productData['id']);
        $productMock->expects($this->once())
            ->method('getStoreId')
            ->willReturn($productData['store_id']);

        $this->dateTimeMock->expects($this->once())
            ->method('gmtDate')
            ->with(StdlibDateTime::DATE_PHP_FORMAT)
            ->willReturn($currentDate);

        $this->ruleResourceMock->expects($this->once())
            ->method('getSortedRulesDataForProduct')
            ->with(
                $productData['id'],
                $customerGroupId,
                $productData['store_id'],
                $currentDate
            )->willReturn($expected);

        $this->assertEquals($expected, $this->model->getAvailableRulesForProduct($productMock, $customerGroupId));
    }

    /**
     * Test getLabelsForRules method
     */
    public function testGetLabelsForRules()
    {
        $labelIds = [1];
        $availableRules = [
            [RuleProductInterface::LABEL_ID => $labelIds[0], RuleProductInterface::LABEL_TEXT_LARGE => 'text']
        ];
        $labelMock = $this->getMockForAbstractClass(LabelInterface::class);
        $expected = [$labelMock];

        $this->searchCriteriaBuilderMock->expects($this->once())
            ->method('addFilter')
            ->with(LabelInterface::LABEL_ID, $labelIds, 'in')
            ->willReturnSelf();

        $searchCriteriaMock = $this->createMock(SearchCriteria::class);
        $this->searchCriteriaBuilderMock->expects($this->once())
            ->method('create')
            ->willReturn($searchCriteriaMock);

        $searchResultsMock = $this->getMockForAbstractClass(LabelSearchResultsInterface::class);
        $searchResultsMock->expects($this->once())
            ->method('getItems')
            ->willReturn($expected);

        $this->labelRepositoryMock->expects($this->once())
            ->method('getList')
            ->with($searchCriteriaMock)
            ->willReturn($searchResultsMock);

        $this->assertEquals($expected, $this->model->getLabelsForRules($availableRules));
    }
}
