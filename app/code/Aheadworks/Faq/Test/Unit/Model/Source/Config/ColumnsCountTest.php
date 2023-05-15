<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */


namespace Aheadworks\Faq\Test\Unit\Model\Source\Config;

use Aheadworks\Faq\Model\Source\Config\ColumnsCount;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;

class ColumnsCountTest extends TestCase
{
    /**
     * @var ColumnsCount
     */
    private $columnCountObject;

    /**
     * Initialize Config
     */
    public function setUp()
    {
        $this->columnCountObject = (new ObjectManager($this))->getObject(ColumnsCount::class);
    }

    /**
     * Get options
     *
     * @covers ColumnsCount::toOptionArray
     */
    public function testToOptionArray()
    {
        $expected = [1 => 1, 2 => 2, 3 => 3];

        $this->assertEquals($expected, $this->columnCountObject->toOptionArray());
    }
}
