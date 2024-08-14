<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Test\Unit\Model\Source\System;

use Aheadworks\OnSale\Model\Source\System\Website;
use Magento\Store\Model\System\Store as SystemStore;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;

/**
 * Class WebsiteTest
 *
 * @package Aheadworks\OnSale\Test\Unit\Model\Source\System
 */
class WebsiteTest extends TestCase
{
    /**
     * @var Website
     */
    private $model;

    /**
     * @var SystemStore|\PHPUnit_Framework_MockObject_MockObject
     */
    private $systemStoreMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->systemStoreMock = $this->createPartialMock(SystemStore::class, ['getWebsiteValuesForForm']);
        $this->model = $objectManager->getObject(
            Website::class,
            [
                'systemStore' => $this->systemStoreMock
            ]
        );
    }

    /**
     * Test toOptionArray method
     */
    public function testToOptionArray()
    {
        $expected = [];
        $this->systemStoreMock->expects($this->once())
            ->method('getWebsiteValuesForForm')
            ->willReturn($expected);

        $this->assertTrue(is_array($this->model->toOptionArray()));
    }
}
