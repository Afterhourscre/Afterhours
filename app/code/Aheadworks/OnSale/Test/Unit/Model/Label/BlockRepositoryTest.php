<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Test\Unit\Model\Label;

use Aheadworks\OnSale\Api\Data\BlockInterface;
use Aheadworks\OnSale\Api\Data\LabelInterface;
use Aheadworks\OnSale\Model\Label\BlockRepository;
use Aheadworks\OnSale\Model\ResourceModel\Rule\Indexer\RuleProduct\RuleProductInterface;
use Magento\Catalog\Model\Product;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\OnSale\Model\Label\Block\Rule\Counter;
use Aheadworks\OnSale\Model\Label\Block\Rule\Loader;
use Aheadworks\OnSale\Model\Label\Block\Factory as BlockFactory;

/**
 * Class BlockRepositoryTest
 *
 * @package Aheadworks\OnSale\Test\Unit\Model\Label
 */
class BlockRepositoryTest extends TestCase
{
    /**
     * @var BlockRepository
     */
    private $model;

    /**
     * @var BlockFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $blockFactoryMock;

    /**
     * @var Loader|\PHPUnit_Framework_MockObject_MockObject
     */
    private $loaderMock;

    /**
     * @var Counter|\PHPUnit_Framework_MockObject_MockObject
     */
    private $counterMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->blockFactoryMock = $this->createPartialMock(BlockFactory::class, ['create']);
        $this->loaderMock = $this->createPartialMock(
            Loader::class,
            ['getAvailableRulesForProduct', 'getLabelsForRules']
        );
        $this->counterMock = $this->createPartialMock(Counter::class, ['reset', 'isLimitReached']);
        $this->model = $objectManager->getObject(
            BlockRepository::class,
            [
                'blockFactory' => $this->blockFactoryMock,
                'loader' => $this->loaderMock,
                'counter' => $this->counterMock
            ]
        );
    }

    /**
     * Testing of getList method
     *
     * @param LabelInterface|\PHPUnit_Framework_MockObject_MockObject $labelMock
     * @param BlockInterface|\PHPUnit_Framework_MockObject_MockObject $blockMock
     * @param bool $isLimitReached
     * @dataProvider getListDataProvider
     */
    public function testGetList($labelMock, $blockMock, $isLimitReached)
    {
        $customerGroupId = 1;
        $productData = ['store_id' => 1];
        $availableRule = [
            RuleProductInterface::LABEL_ID => 1,
            RuleProductInterface::LABEL_TEXT_LARGE => 'large',
            RuleProductInterface::LABEL_TEXT_MEDIUM => 'medium',
            RuleProductInterface::LABEL_TEXT_SMALL => 'small'
        ];
        $labelData = [LabelInterface::LABEL_ID => 1, LabelInterface::POSITION => 1];
        $availableRules = [$availableRule];
        $labels = [$labelMock];
        $blockItems = $isLimitReached ? [] : [$blockMock];
        $variableValues = [];

        $productMock = $this->createPartialMock(Product::class, ['getStoreId']);
        $productMock->expects($this->once())
            ->method('getStoreId')
            ->willReturn($productData['store_id']);

        $this->loaderMock->expects($this->once())
            ->method('getAvailableRulesForProduct')
            ->willReturn($availableRules);
        $this->loaderMock->expects($this->once())
            ->method('getLabelsForRules')
            ->with($availableRules)
            ->willReturn($labels);
        $this->counterMock->expects($this->once())
            ->method('reset')
            ->willReturnSelf();

        $labelMock->expects($this->atLeastOnce())
            ->method('getLabelId')
            ->willReturn($labelData[LabelInterface::LABEL_ID]);
        $labelMock->expects($this->once())
            ->method('getPosition')
            ->willReturn($labelData[LabelInterface::POSITION]);

        $this->counterMock->expects($this->once())
            ->method('isLimitReached')
            ->with($labelData[LabelInterface::POSITION], $productData['store_id'])
            ->willReturn($isLimitReached);

        if (!$isLimitReached) {
            $labelTexts = [
                RuleProductInterface::LABEL_TEXT_LARGE => $availableRule[RuleProductInterface::LABEL_TEXT_LARGE],
                RuleProductInterface::LABEL_TEXT_MEDIUM => $availableRule[RuleProductInterface::LABEL_TEXT_MEDIUM],
                RuleProductInterface::LABEL_TEXT_SMALL => $availableRule[RuleProductInterface::LABEL_TEXT_SMALL]
            ];
            $this->blockFactoryMock->expects($this->once())
                ->method('create')
                ->with($labelMock, $labelTexts, $productMock)
                ->willReturn($blockMock);
        }

        $this->assertEquals($blockItems, $this->model->getList($productMock, $customerGroupId));
    }

    /**
     * Data provider for getList test
     *
     * @return array
     */
    public function getListDataProvider()
    {
        $labelMock1 = $this->getMockForAbstractClass(LabelInterface::class);
        $blockMock1 = $this->getMockForAbstractClass(BlockInterface::class);
        $labelMock2 = $this->getMockForAbstractClass(LabelInterface::class);
        $blockMock2 = $this->getMockForAbstractClass(BlockInterface::class);

        return [
            [$labelMock1, $blockMock1, false],
            [$labelMock2, $blockMock2, true]
        ];
    }
}
