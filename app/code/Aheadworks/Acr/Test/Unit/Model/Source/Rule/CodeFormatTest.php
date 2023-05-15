<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Acr\Test\Unit\Model\Source\Rule;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;
use Aheadworks\Acr\Model\Source\Rule\CodeFormat;

/**
 * Class CodeFormatTest
 * Test for \Aheadworks\Acr\Model\Source\CodeFormat
 *
 * @package Aheadworks\Acr\Test\Unit\Model\Source\Rule
 */
class CodeFormatTest extends TestCase
{
    /**
     * @var CodeFormat
     */
    private $model;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->model = $objectManager->getObject(
            CodeFormat::class,
            []
        );
    }

    /**
     * Test toOptionArray method
     */
    public function testToOptionArray()
    {
        $this->assertTrue(is_array($this->model->toOptionArray()));
    }
}
