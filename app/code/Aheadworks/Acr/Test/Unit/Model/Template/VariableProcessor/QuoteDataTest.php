<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Acr\Test\Unit\Model\Template\VariableProcessor;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;
use Aheadworks\Acr\Model\Template\VariableProcessor\QuoteData;
use Magento\Quote\Model\ResourceModel\Quote\CollectionFactory as QuoteCollectionFactory;
use Magento\Quote\Model\ResourceModel\Quote\Collection as QuoteCollection;
use Magento\Quote\Model\Quote;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Quote\Api\Data\CartInterface;
use Aheadworks\Acr\Model\Hydrator\Quote as HydratorQuote;

/**
 * Class QuoteDataTest
 * @package Aheadworks\Acr\Test\Unit\Model\Template\VariableProcessor
 */
class QuoteDataTest extends TestCase
{
    /**
     * @var QuoteData
     */
    private $quoteData;

    /**
     * @var QuoteCollectionFactory
     */
    private $quoteCollectionFactoryMock;

    /**
     * @var DataObjectProcessor
     */
    private $hydratorMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->quoteCollectionFactoryMock = $this->createMock(QuoteCollectionFactory::class);
        $this->hydratorMock = $this->createMock(HydratorQuote::class);

        $this->quoteData = $objectManager->getObject(
            QuoteData::class,
            [
                'quoteCollectionFactory' => $this->quoteCollectionFactoryMock,
                'hydrator' => $this->hydratorMock,
            ]
        );
    }

    /**
     * Test Process method
     */
    public function testProcess()
    {
        $quoteData = [
            'test1' => 'test',
            'test2' => 'test',
            'test3' => 'test'
        ];
        $quote = $this->createMock(Quote::class);
        $this->hydratorMock->expects($this->once())
            ->method('extract')
            ->with($quote)
            ->willReturn($quoteData);
        $this->assertSame($quoteData, $this->quoteData->process($quote, []));
    }

    /**
     * Test ProcessTest method
     */
    public function testProcessTest()
    {
        $field = 'is_active';
        $quoteData = [
            'test1' => 'test',
            'test2' => 'test',
            'test3' => 'test'
        ];
        $isActive = 1;
        $quoteCollection = $this->createMock(QuoteCollection::class);
        $quote = $this->createMock(Quote::class);
        $this->quoteCollectionFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($quoteCollection);
        $quoteCollection->expects($this->once())
            ->method('addFilter')
            ->with($field, $isActive)
            ->willReturnSelf();
        $quoteCollection->expects($this->once())
            ->method('getFirstItem')
            ->willReturn($quote);
        $this->hydratorMock->expects($this->once())
            ->method('extract')
            ->with($quote)
            ->willReturn($quoteData);
        $this->assertSame($quoteData, $this->quoteData->processTest([]));
    }
}
