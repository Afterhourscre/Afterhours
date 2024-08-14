<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Coupongenerator\Test\Unit\Model\Source\Rule;

use Aheadworks\Coupongenerator\Model\Source\Rule\Name;
use Aheadworks\Coupongenerator\Model\ResourceModel\Salesrule\CollectionFactory as SalesruleCollectionFactory;
use Aheadworks\Coupongenerator\Model\ResourceModel\Salesrule\Collection as SalesruleCollection;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Coupongenerator\Model\Source\Rule\Name
 */
class NameTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Name
     */
    private $sourceModel;

    /**
     * @var SalesruleCollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $salesruleCollectionFactoryMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->salesruleCollectionFactoryMock = $this->getMockBuilder(SalesruleCollectionFactory::class)
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->sourceModel = $objectManager->getObject(
            Name::class,
            [
                'salesruleCollectionFactory' => $this->salesruleCollectionFactoryMock
            ]
        );
    }

    /**
     * Test toOptionArray method
     */
    public function testToOptionArray()
    {
        $salesruleCollectionMock = $this->getMockBuilder(SalesruleCollection::class)
            ->setMethods(['setActiveRules', 'toOptionArray'])
            ->disableOriginalConstructor()
            ->getMock();

        $options = [
            ['value' => '1', 'label' => 'Rule1' ],
            ['value' => '2', 'label' => 'Rule2' ]
        ];

        $salesruleCollectionMock->expects($this->once())
            ->method('setActiveRules')
            ->willReturnSelf();
        $salesruleCollectionMock->expects($this->once())
            ->method('toOptionArray')
            ->willReturn($options);

        $this->salesruleCollectionFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($salesruleCollectionMock);

        $this->assertTrue(is_array($this->sourceModel->toOptionArray()));
    }
}
