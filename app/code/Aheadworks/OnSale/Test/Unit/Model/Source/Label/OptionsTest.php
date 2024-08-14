<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Test\Unit\Model\Source\Label;

use Aheadworks\OnSale\Api\Data\LabelSearchResultsInterface;
use Aheadworks\OnSale\Model\Source\Label\Options;
use Magento\Framework\Api\SearchCriteria;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;
use Aheadworks\OnSale\Api\LabelRepositoryInterface;
use Magento\Framework\Convert\DataObject;
use Aheadworks\OnSale\Api\Data\LabelInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;

/**
 * Class OptionsTest
 *
 * @package Aheadworks\OnSale\Test\Unit\Model\Source\Label
 */
class OptionsTest extends TestCase
{
    /**
     * @var Options
     */
    private $model;

    /**
     * @var LabelRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $labelRepositoryMock;

    /**
     * @var SearchCriteriaBuilder|\PHPUnit_Framework_MockObject_MockObject
     */
    private $searchCriteriaBuilderMock;

    /**
     * @var DataObject|\PHPUnit_Framework_MockObject_MockObject
     */
    private $objectConverterMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->labelRepositoryMock = $this->getMockForAbstractClass(LabelRepositoryInterface::class);
        $this->searchCriteriaBuilderMock = $this->createPartialMock(SearchCriteriaBuilder::class, ['create']);
        $this->objectConverterMock = $this->createPartialMock(DataObject::class, ['toOptionArray']);
        $this->model = $objectManager->getObject(
            Options::class,
            [
                'labelRepository' => $this->labelRepositoryMock,
                'searchCriteriaBuilder' => $this->searchCriteriaBuilderMock,
                'objectConverter' => $this->objectConverterMock
            ]
        );
    }

    /**
     * Test toOptionArray method
     */
    public function testToOptionArray()
    {
        $labelMock = $this->getMockForAbstractClass(LabelInterface::class);
        $labels = [$labelMock];
        $expected = [];

        $searchCriteriaMock = $this->getMockForAbstractClass(SearchCriteria::class);
        $this->searchCriteriaBuilderMock->expects($this->once())
            ->method('create')
            ->willReturn($searchCriteriaMock);
        $labelSearchResultsMock = $this->getMockForAbstractClass(LabelSearchResultsInterface::class);
        $this->labelRepositoryMock->expects($this->once())
            ->method('getList')
            ->with($searchCriteriaMock)
            ->willReturn($labelSearchResultsMock);

        $labelSearchResultsMock->expects($this->once())
            ->method('getItems')
            ->willReturn($labels);

        $this->objectConverterMock->expects($this->once())
            ->method('toOptionArray')
            ->with($labels, LabelInterface::LABEL_ID, LabelInterface::NAME)
            ->willReturn($expected);

        $this->assertTrue(is_array($this->model->toOptionArray()));
    }
}
