<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Test\Unit\Model\Rule;

use Aheadworks\OnSale\Model\Rule\ReindexNotice\Flag as ReindexFlag;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;
use Aheadworks\OnSale\Model\Rule\ReindexNotice;

/**
 * Class ReindexNoticeTest
 *
 * @package Aheadworks\OnSale\Model\Rule
 */
class ReindexNoticeTest extends TestCase
{
    /**#@+
     * Constants defined for testing
     */
    const STATE_ON = 1;
    const STATE_OFF = 0;
    /**#@-*/

    /**
     * @var ReindexNotice
     */
    private $model;

    /**
     * @var ReindexFlag|\PHPUnit_Framework_MockObject_MockObject
     */
    private $flagMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->flagMock = $this->createPartialMock(
            ReindexFlag::class,
            ['loadSelf', 'setState', 'save', 'getState']
        );

        $this->model = $objectManager->getObject(
            ReindexNotice::class,
            [
                'flag' => $this->flagMock
            ]
        );
    }

    /**
     * Test for setEnabled method
     */
    public function testSetEnabled()
    {
        $this->flagMock->expects($this->once())
            ->method('loadSelf')
            ->willReturnSelf();
        $this->flagMock->expects($this->once())
            ->method('setState')
            ->with(self::STATE_ON)
            ->willReturnSelf();
        $this->flagMock->expects($this->once())
            ->method('save')
            ->willReturnSelf();

        $this->model->setEnabled();
    }

    /**
     * Test for setDisabled method
     */
    public function testSetDisabled()
    {
        $this->flagMock->expects($this->once())
            ->method('loadSelf')
            ->willReturnSelf();
        $this->flagMock->expects($this->once())
            ->method('setState')
            ->with(self::STATE_OFF)
            ->willReturnSelf();
        $this->flagMock->expects($this->once())
            ->method('save')
            ->willReturnSelf();

        $this->model->setDisabled();
    }

    /**
     * Test for isEnabled method
     */
    public function testIsEnabled()
    {
        $this->flagMock->expects($this->once())
            ->method('loadSelf')
            ->willReturnSelf();
        $this->flagMock->expects($this->once())
            ->method('getState')
            ->willReturn(self::STATE_ON);

        $this->assertSame(self::STATE_ON, $this->model->isEnabled());
    }

    /**
     * Test for isEnabled method on exception
     */
    public function testIsEnabledOnException()
    {
        $this->flagMock->expects($this->once())
            ->method('loadSelf')
            ->willThrowException(new LocalizedException(__('test exception')));

        $this->assertSame(self::STATE_OFF, $this->model->isEnabled());
    }
}
