<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Test\Unit\Model\Rule\Validator;

use Aheadworks\OnSale\Model\Rule\Validator\Date;
use Aheadworks\OnSale\Api\Data\RuleInterface;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Class DateTest
 *
 * @package Aheadworks\OnSale\Test\Unit\Model\Rule\Validator
 */
class DateTest extends TestCase
{
    /**
     * @var Date
     */
    private $model;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->model = $objectManager->getObject(Date::class);
    }

    /**
     * Test for isValid method
     *
     * @dataProvider isValidDataProvider
     * @param RuleInterface $ruleMock
     * @param bool $result
     */
    public function testIsValid($ruleMock, $result)
    {
        $this->assertSame($result, $this->model->isValid($ruleMock));
    }

    /**
     * Data provider for testIsValid method
     */
    public function isValidDataProvider()
    {
        $date1 = '2018-10-10';
        $date2 = '2018-10-12';

        $validRuleMock = $this->getMockForAbstractClass(RuleInterface::class);
        $validRuleMock->expects($this->any())
            ->method('getFromDate')
            ->willReturn($date1);
        $validRuleMock->expects($this->any())
            ->method('getToDate')
            ->willReturn($date2);

        $invalidRuleMock = $this->getMockForAbstractClass(RuleInterface::class);
        $invalidRuleMock->expects($this->any())
            ->method('getFromDate')
            ->willReturn($date2);
        $invalidRuleMock->expects($this->any())
            ->method('getToDate')
            ->willReturn($date1);

        return [
            'valid' => [$validRuleMock, true],
            'invalid' => [$invalidRuleMock, false],
        ];
    }
}
