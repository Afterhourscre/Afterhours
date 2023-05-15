<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Test\Unit\Model\Label;

use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Aheadworks\OnSale\Api\Data\LabelInterface;
use Aheadworks\OnSale\Api\LabelRepositoryInterface;
use Aheadworks\OnSale\Api\Data\LabelInterfaceFactory;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\OnSale\Model\Label\Copier;

/**
 * Class CopierTest
 *
 * @package Aheadworks\OnSale\Test\Unit\Model\Label
 */
class CopierTest extends TestCase
{
    /**
     * @var Copier
     */
    private $model;

    /**
     * @var DataObjectHelper|\PHPUnit_Framework_MockObject_MockObject
     */
    private $dataObjectHelperMock;

    /**
     * @var DataObjectProcessor|\PHPUnit_Framework_MockObject_MockObject
     */
    private $dataObjectProcessorMock;

    /**
     * @var LabelRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $labelRepositoryMock;

    /**
     * @var LabelInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $labelFactoryMock;

    /**
     * Init mocks for tests
     *
     * @return void
     * @throws \ReflectionException
     */
    protected function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->dataObjectHelperMock = $this->createMock(DataObjectHelper::class);
        $this->dataObjectProcessorMock = $this->createMock(DataObjectProcessor::class);
        $this->labelRepositoryMock = $this->createMock(LabelRepositoryInterface::class);
        $this->labelFactoryMock = $this->createMock(LabelInterfaceFactory::class);
        $this->model = $objectManager->getObject(
            Copier::class,
            [
                'dataObjectHelper' => $this->dataObjectHelperMock,
                'dataObjectProcessor' => $this->dataObjectProcessorMock,
                'labelRepository' => $this->labelRepositoryMock,
                'labelFactory' => $this->labelFactoryMock
            ]
        );
    }

    /**
     * Test copy method
     *
     * @param bool $throwException
     * @dataProvider copyProvider
     * @throws LocalizedException
     * @throws \ReflectionException
     */
    public function testCopy($throwException)
    {
        $exception = new LocalizedException(__('Save exception message.'));
        $labelData = [
            LabelInterface::LABEL_ID => 1,
            LabelInterface::NAME => 'label name',
        ];
        $newLabelData = [
            LabelInterface::NAME => 'label name',
        ];

        /** @var LabelInterface|\PHPUnit_Framework_MockObject_MockObject $labelMock */
        $labelMock = $this->createMock(LabelInterface::class);
        $this->dataObjectProcessorMock->expects($this->once())
            ->method('buildOutputDataArray')
            ->with($labelMock, LabelInterface::class)
            ->willReturn($labelData);
        $this->labelFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($labelMock);
        $this->dataObjectHelperMock->expects($this->once())
            ->method('populateWithArray')
            ->with($labelMock, $newLabelData, LabelInterface::class);

        if ($throwException) {
            $this->labelRepositoryMock->expects($this->once())
                ->method('save')
                ->with($labelMock)
                ->willThrowException($exception);
            $this->expectException(LocalizedException::class);
            $this->expectExceptionMessage('Save exception message.');
            $this->model->copy($labelMock);
        } else {
            $this->labelRepositoryMock->expects($this->once())
                ->method('save')
                ->with($labelMock)
                ->willReturn($labelMock);

            $this->assertSame($labelMock, $this->model->copy($labelMock));
        }
    }

    /**
     * Data provider for copy method
     *
     * @return array
     */
    public function copyProvider()
    {
        return [[true], [false]];
    }
}
