<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Coupongenerator\Test\Unit\Block\Adminhtml\Rule\Edit;

use Aheadworks\Coupongenerator\Block\Adminhtml\Rule\Edit\SaveAndContinueButton;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Coupongenerator\Block\Adminhtml\Rule\Edit\SaveAndContinueButton
 */
class SaveAndContinueButtonTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var SaveAndContinueButton
     */
    private $button;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->button = $objectManager->getObject(
            SaveAndContinueButton::class,
            []
        );
    }

    /**
     * Test getButtonData method
     */
    public function testGetButtonData()
    {
        $this->assertTrue(is_array($this->button->getButtonData()));
    }
}
