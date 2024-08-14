<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Coupongenerator\Test\Unit\Model\Source\Coupon;

use Aheadworks\Coupongenerator\Model\Source\Coupon\Status;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Coupongenerator\Model\Source\Coupon\Status
 */
class StatusTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Status
     */
    private $sourceModel;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->sourceModel = $objectManager->getObject(
            Status::class,
            []
        );
    }

    /**
     * Test toOptionArray method
     */
    public function testToOptionArray()
    {
        $this->assertTrue(is_array($this->sourceModel->toOptionArray()));
    }

    /**
     * Test getOptions method
     */
    public function testGetOptions()
    {
        $this->assertTrue(is_array($this->sourceModel->getOptions()));
    }

    /**
     * Test getOptionByValue method
     *
     * @param int $value
     * @param string $expected
     * @dataProvider getOptionByValueDataProvider
     */
    public function testGetOptionByValue($value, $expected)
    {
        $this->assertEquals($expected, $this->sourceModel->getOptionByValue($value));
    }

    /**
     * Data provider for testGetOptionByValue method
     *
     * @return array
     */
    public function getOptionByValueDataProvider()
    {
        return [
            [Status::AVAILABLE_VALUE, 'Active'],
            [Status::EXPIRED_VALUE, 'Expired'],
            [Status::USED_VALUE, 'Used'],
            [Status::DEACTIVATED_VALUE, 'Deactivated']
        ];
    }
}
