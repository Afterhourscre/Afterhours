<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */


namespace Aheadworks\Faq\Test\Unit\Model\Category\Source;

use Aheadworks\Faq\Model\Category\Source\IsEnable;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;

/**
 * Test for IsEnable
 *
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class IsEnableTest extends TestCase
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var IsEnable
     */
    private $isEnableObject;

    /**
     * Initialize model
     */
    public function setUp()
    {
        $this->objectManager = new ObjectManager($this);

        $this->isEnableObject = $this->objectManager->getObject(IsEnable::class);
    }

    /**
     * Get options
     *
     * @covers IsEnable::toOptionArray
     */
    public function testToOptionArray()
    {
        $statuses = [['label' => __('Enabled'), 'value' => 1], ['label' => __('Disabled'), 'value' => 0]];

        $this->assertEquals($statuses, $this->isEnableObject->toOptionArray());
    }
}
