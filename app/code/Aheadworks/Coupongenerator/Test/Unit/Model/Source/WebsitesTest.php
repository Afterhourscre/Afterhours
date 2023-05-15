<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Coupongenerator\Test\Unit\Model\Source;

use Aheadworks\Coupongenerator\Model\Source\Websites;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Store\Api\WebsiteRepositoryInterface;
use Magento\Framework\Convert\DataObject;
use Magento\Store\Api\Data\WebsiteInterface;

/**
 * Test for \Aheadworks\Coupongenerator\Model\Source\Websites
 */
class WebsitesTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Websites
     */
    private $sourceModel;

    /**
     * @var WebsiteRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $websiteRepositoryMock;

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

        $this->websiteRepositoryMock = $this->getMockForAbstractClass(WebsiteRepositoryInterface::class);
        $this->objectConverterMock = $this->getMockBuilder(DataObject::class)
            ->setMethods([])
            ->disableOriginalConstructor()
            ->getMock();

        $this->sourceModel = $objectManager->getObject(
            Websites::class,
            [
                'websiteRepository' => $this->websiteRepositoryMock,
                'objectConverter' => $this->objectConverterMock
            ]
        );
    }

    /**
     * Test toOptionArray method
     */
    public function testToOptionArray()
    {
        $websiteOneMock = $this->getMockForAbstractClass(WebsiteInterface::class);
        $websiteOneMock->expects($this->any())
            ->method('getId')
            ->willReturn(1);
        $websiteOneMock->expects($this->any())
            ->method('getName')
            ->willReturn('Website1');

        $websiteTwoMock = $this->getMockForAbstractClass(WebsiteInterface::class);
        $websiteTwoMock->expects($this->any())
            ->method('getId')
            ->willReturn(2);
        $websiteTwoMock->expects($this->any())
            ->method('getName')
            ->willReturn('Website2');

        $websites = [$websiteOneMock, $websiteTwoMock];
        $this->websiteRepositoryMock->expects($this->once())
            ->method('getList')
            ->willReturn($websites);

        $options = [
            ['label' => 'Website1', 'value' => '1'],
            ['label' => 'Website2', 'value' => '2']
        ];
        $this->objectConverterMock->expects($this->once())
            ->method('toOptionArray')
            ->with($websites, 'id', 'name')
            ->willReturn($options);

        $this->assertTrue(is_array($this->sourceModel->toOptionArray()));
    }
}
