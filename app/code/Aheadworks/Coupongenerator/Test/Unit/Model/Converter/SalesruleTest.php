<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Coupongenerator\Test\Unit\Model\Converter;

use Aheadworks\Coupongenerator\Model\Converter\Salesrule;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\Coupongenerator\Api\Data\SalesruleInterfaceFactory;
use Aheadworks\Coupongenerator\Api\Data\SalesruleInterface;
use Magento\SalesRule\Api\Data\RuleInterface;
use Magento\SalesRule\Api\Data\RuleExtensionFactory;
use Magento\SalesRule\Api\Data\RuleExtension;
use Magento\SalesRule\Api\Data\CouponGenerationSpecInterface;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\Api\DataObjectHelper;

/**
 * Test for \Aheadworks\Coupongenerator\Model\Converter\Salesrule
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class SalesruleTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Salesrule
     */
    private $model;

    /**
     * @var DataObjectProcessor|\PHPUnit_Framework_MockObject_MockObject
     */
    private $dataObjectProcessorMock;

    /**
     * @var DataObjectHelper|\PHPUnit_Framework_MockObject_MockObject
     */
    private $dataObjectHelperMock;

    /**
     * @var RuleExtensionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $magentoRuleExtensionFactoryMock;

    /**
     * @var SalesruleInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $salesruleFactoryMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->dataObjectProcessorMock = $this->getMockBuilder(DataObjectProcessor::class)
            ->setMethods(['buildOutputDataArray'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->dataObjectHelperMock = $this->getMockBuilder(DataObjectHelper::class)
            ->setMethods(['populateWithArray'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->magentoRuleExtensionFactoryMock = $this->getMockBuilder(RuleExtensionFactory::class)
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->salesruleFactoryMock = $this->getMockBuilder(SalesruleInterfaceFactory::class)
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->model = $objectManager->getObject(
            Salesrule::class,
            [
                'dataObjectProcessor' => $this->dataObjectProcessorMock,
                'dataObjectHelper' => $this->dataObjectHelperMock,
                'magentoRuleExtensionFactory' => $this->magentoRuleExtensionFactoryMock,
                'salesruleFactory' => $this->salesruleFactoryMock
            ]
        );
    }

    /**
     * Test toFormData method
     * @param array $ruleData
     * @dataProvider getRuleDataProvider
     */
    public function testToFormData($ruleData)
    {
        $salesruleDataMock = $this->getMockForAbstractClass(SalesruleInterface::class);
        $salesruleDataMock
            ->expects($this->once())
            ->method('getExpirationDays')
            ->willReturn($ruleData['aw_coupongenerator_expiration']);
        $salesruleDataMock
            ->expects($this->once())
            ->method('getCouponLength')
            ->willReturn($ruleData['aw_coupongenerator_length']);
        $salesruleDataMock
            ->expects($this->once())
            ->method('getCodeFormat')
            ->willReturn($ruleData['aw_coupongenerator_format']);
        $salesruleDataMock
            ->expects($this->once())
            ->method('getCodePrefix')
            ->willReturn($ruleData['aw_coupongenerator_prefix']);
        $salesruleDataMock
            ->expects($this->once())
            ->method('getCodeSuffix')
            ->willReturn($ruleData['aw_coupongenerator_suffix']);
        $salesruleDataMock
            ->expects($this->once())
            ->method('getCodeDash')
            ->willReturn($ruleData['aw_coupongenerator_dash']);

        $ruleExtensionMock = $this->getMockBuilder(RuleExtension::class)
            ->setMethods(['getAwCoupongeneratorData'])
            ->getMock();
        $ruleExtensionMock
            ->expects($this->atLeastOnce())
            ->method('getAwCoupongeneratorData')
            ->willReturn($salesruleDataMock);

        $ruleMock = $this->getMockForAbstractClass(RuleInterface::class);
        $ruleMock
            ->expects($this->once())
            ->method('getExtensionAttributes')
            ->willReturn($ruleExtensionMock);

        $this->dataObjectProcessorMock
            ->expects($this->once())
            ->method('buildOutputDataArray')
            ->willReturn($ruleData);

        $this->assertTrue(is_array($this->model->toFormData($ruleMock)));
    }

    /**
     * Test populateWithFormData method
     * @param array $ruleData
     * @dataProvider getRuleDataProvider
     */
    public function testPopulateWithFormData($ruleData)
    {
        $salesruleDataMock = $this->getMockForAbstractClass(SalesruleInterface::class);
        $salesruleDataMock
            ->expects($this->once())
            ->method('setExpirationDays')
            ->willReturnSelf();
        $salesruleDataMock
            ->expects($this->once())
            ->method('setCouponLength')
            ->willReturnSelf();
        $salesruleDataMock
            ->expects($this->once())
            ->method('setCodeFormat')
            ->willReturnSelf();
        $salesruleDataMock
            ->expects($this->once())
            ->method('setCodePrefix')
            ->willReturnSelf();
        $salesruleDataMock
            ->expects($this->once())
            ->method('setCodeSuffix')
            ->willReturnSelf();
        $salesruleDataMock
            ->expects($this->once())
            ->method('setCodeDash')
            ->willReturnSelf();

        $ruleExtensionMock = $this->getMockBuilder(RuleExtension::class)
            ->setMethods(['getAwCoupongeneratorData', 'setAwCoupongeneratorData'])
            ->getMock();
        $ruleExtensionMock
            ->expects($this->atLeastOnce())
            ->method('getAwCoupongeneratorData')
            ->willReturn($salesruleDataMock);
        $ruleExtensionMock
            ->expects($this->atLeastOnce())
            ->method('setAwCoupongeneratorData')
            ->willReturnSelf();

        $ruleMock = $this->getMockForAbstractClass(RuleInterface::class);
        $ruleMock
            ->expects($this->once())
            ->method('setExtensionAttributes')
            ->willReturnSelf();
        $ruleMock
            ->expects($this->once())
            ->method('getExtensionAttributes')
            ->willReturn($ruleExtensionMock);

        $this->dataObjectHelperMock
            ->expects($this->once())
            ->method('populateWithArray')
            ->willReturnSelf();

        $this->assertEquals($ruleMock, $this->model->populateWithFormData($ruleMock, $ruleData));
    }

    /**
     * Test populateWithFormData method, if no extension attribute is set
     * @param array $ruleData
     * @dataProvider getRuleDataProvider
     */
    public function testPopulateWithFormDataNoExtensionAttribute($ruleData)
    {
        $salesruleDataMock = $this->getMockForAbstractClass(SalesruleInterface::class);
        $salesruleDataMock
            ->expects($this->once())
            ->method('setExpirationDays')
            ->willReturnSelf();
        $salesruleDataMock
            ->expects($this->once())
            ->method('setCouponLength')
            ->willReturnSelf();
        $salesruleDataMock
            ->expects($this->once())
            ->method('setCodeFormat')
            ->willReturnSelf();
        $salesruleDataMock
            ->expects($this->once())
            ->method('setCodePrefix')
            ->willReturnSelf();
        $salesruleDataMock
            ->expects($this->once())
            ->method('setCodeSuffix')
            ->willReturnSelf();
        $salesruleDataMock
            ->expects($this->once())
            ->method('setCodeDash')
            ->willReturnSelf();
        $this->salesruleFactoryMock
            ->expects($this->once())
            ->method('create')
            ->willReturn($salesruleDataMock);

        $ruleExtensionMock = $this->getMockBuilder(RuleExtension::class)
            ->setMethods(['getAwCoupongeneratorData', 'setAwCoupongeneratorData'])
            ->getMock();
        $ruleExtensionMock
            ->expects($this->atLeastOnce())
            ->method('getAwCoupongeneratorData')
            ->willReturn(null);
        $ruleExtensionMock
            ->expects($this->atLeastOnce())
            ->method('setAwCoupongeneratorData')
            ->willReturnSelf();
        $this->magentoRuleExtensionFactoryMock
            ->expects($this->once())
            ->method('create')
            ->willReturn($ruleExtensionMock);

        $ruleMock = $this->getMockForAbstractClass(RuleInterface::class);
        $ruleMock
            ->expects($this->once())
            ->method('setExtensionAttributes')
            ->willReturnSelf();
        $ruleMock
            ->expects($this->once())
            ->method('getExtensionAttributes')
            ->willReturn(null);

        $this->dataObjectHelperMock
            ->expects($this->once())
            ->method('populateWithArray')
            ->willReturnSelf();

        $this->assertEquals($ruleMock, $this->model->populateWithFormData($ruleMock, $ruleData));
    }

    /**
     * Data provider for tests
     *
     * @return array
     */
    public function getRuleDataProvider()
    {
        return [
            [
                [
                    'rule_id' => '5',
                    'name' => 'Test rule',
                    'description' => 'Test rule description',
                    'website_ids' => [
                        0 => '1'
                    ],
                    'customer_group_ids' => [
                        0 => '0',
                        1 => '1',
                        2 => '2'
                    ],
                    'from_date' => '2016-06-24',
                    'uses_per_customer' => '1',
                    'is_active' => '1',
                    'stop_rule_processing' => 'true',
                    'is_advanced' => 'true',
                    'product_ids' => null,
                    'sort_order' => '0',
                    'simple_action' => RuleInterface::DISCOUNT_ACTION_BY_PERCENT,
                    'discount_amount' => '10',
                    'discount_step' => '0',
                    'apply_to_shipping' => '',
                    'stop_rules_processing' => '1',
                    'times_used' => '4',
                    'is_rss' => 'true',
                    'coupon_type' => RuleInterface::COUPON_TYPE_SPECIFIC_COUPON,
                    'use_auto_generation' => 'true',
                    'uses_per_coupon' => '1',
                    'sort_order' => '0',
                    'aw_coupongenerator_expiration' => '5',
                    'aw_coupongenerator_length' => '12',
                    'aw_coupongenerator_format' => CouponGenerationSpecInterface::COUPON_FORMAT_ALPHANUMERIC,
                    'aw_coupongenerator_prefix' => 'CCG',
                    'aw_coupongenerator_suffix' => 'Z',
                    'aw_coupongenerator_dash' => '',
                    'discount_qty' => '0',
                    'extension_attributes' => [
                        'aw_coupongenerator_data' => [
                            'id' => '1',
                            'rule_id' => '5',
                            'expiration_days' => '5',
                            'coupon_length' => '12',
                            'code_format' => CouponGenerationSpecInterface::COUPON_FORMAT_ALPHANUMERIC,
                            'code_prefix' => 'CCG',
                            'code_suffix' => 'Z',
                            'code_dash' => ''
                        ]
                    ],
                    'condition' => [
                        'condition_type' => Magento\SalesRule\Model\Rule\Condition\Combine::class,
                        'aggregator_type' => 'all',
                        'value' => '1'
                    ],
                    'action_condition' => [
                        'condition_type' => Magento\SalesRule\Model\Rule\Condition\Product\Combine::class,
                        'aggregator_type' => 'all',
                        'value' => '1'
                    ]
                ]
            ]
        ];
    }
}
