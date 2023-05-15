<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Test\Unit\Model\Rule\Validator;

use Aheadworks\OnSale\Api\Data\RuleInterface;
use Aheadworks\OnSale\Api\Data\LabelTextStoreValueInterface;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\OnSale\Model\Rule\Validator\LabelText;

/**
 * Class LabelTextTest
 *
 * @package Aheadworks\OnSale\Test\Unit\Model\Rule\Validator
 */
class LabelTextTest extends TestCase
{
    /**
     * @var LabelText
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
        $this->model = $objectManager->getObject(LabelText::class);
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
        $firstStoreId = 1;
        $secondStoreId = 2;

        $storeValue1 = $this->getMockForAbstractClass(LabelTextStoreValueInterface::class);
        $storeValue2 = $this->getMockForAbstractClass(LabelTextStoreValueInterface::class);
        $storeValue3 = $this->getMockForAbstractClass(LabelTextStoreValueInterface::class);

        $storeValue1->expects($this->any())
            ->method('getStoreId')
            ->willReturn($firstStoreId);
        $storeValue2->expects($this->any())
            ->method('getStoreId')
            ->willReturn($secondStoreId);
        $storeValue3->expects($this->any())
            ->method('getStoreId')
            ->willReturn($firstStoreId);

        $validRuleMock = $this->getMockForAbstractClass(RuleInterface::class);
        $validSetOfStoreValues = [$storeValue1, $storeValue2];
        $validRuleMock->expects($this->any())
            ->method('getFrontendLabelTextStoreValues')
            ->willReturn($validSetOfStoreValues);

        $invalidRuleMock = $this->getMockForAbstractClass(RuleInterface::class);
        $invalidSetOfStoreValues = [$storeValue1, $storeValue2, $storeValue3];
        $invalidRuleMock->expects($this->any())
            ->method('getFrontendLabelTextStoreValues')
            ->willReturn($invalidSetOfStoreValues);

        return [
            'valid' => [$validRuleMock, true],
            'invalid' => [$invalidRuleMock, false],
        ];
    }
}
