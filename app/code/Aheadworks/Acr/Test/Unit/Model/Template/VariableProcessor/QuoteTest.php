<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Acr\Test\Unit\Model\Template\VariableProcessor;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;
use Aheadworks\Acr\Model\Template\VariableProcessor\Quote;
use Magento\Quote\Model\ResourceModel\Quote\CollectionFactory as QuoteCollectionFactory;
use Magento\Quote\Model\ResourceModel\Quote\Collection as QuoteCollection;
use Magento\Quote\Model\Quote as QuoteModel;
use Aheadworks\Acr\Model\Source\Email\Variables;

/**
 * Class QuoteTest
 * @package Aheadworks\Acr\Test\Unit\Model\Template\VariableProcessor
 */
class QuoteTest extends TestCase
{
    /**
     * @var Quote
     */
    private $quote;

    /**
     * @var QuoteCollectionFactory
     */
    private $quoteCollectionFactoryMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->quoteCollectionFactoryMock = $this->createMock(QuoteCollectionFactory::class);

        $this->quote = $objectManager->getObject(
            Quote::class,
            [
                'quoteCollectionFactory' => $this->quoteCollectionFactoryMock,
            ]
        );
    }

    /**
     * Test ProcessTest method
     */
    public function testProcessTest()
    {
        $field = 'is_active';
        $isActive = 1;
        $quoteCollection = $this->createMock(QuoteCollection::class);
        $quote = $this->createMock(QuoteModel::class);
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
        $this->assertSame([Variables::QUOTE => $quote], $this->quote->processTest([]));
    }
}
