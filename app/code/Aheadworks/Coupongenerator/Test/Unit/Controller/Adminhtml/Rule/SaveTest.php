<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Coupongenerator\Test\Unit\Controller\Adminhtml\Rule;

use Aheadworks\Coupongenerator\Controller\Adminhtml\Rule\Save;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Backend\Model\View\Result\RedirectFactory;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\Message\ManagerInterface;
use Magento\SalesRule\Api\RuleRepositoryInterface;
use Magento\SalesRule\Api\Data\RuleInterface;
use Aheadworks\Coupongenerator\Model\SalesruleRepository;
use Magento\SalesRule\Api\Data\CouponGenerationSpecInterface;
use Aheadworks\Coupongenerator\Model\Converter\Salesrule as SalesruleConverter;
use Magento\SalesRule\Api\Data\RuleInterfaceFactory;

/**
 * Test for \Aheadworks\Coupongenerator\Controller\Adminhtml\Rule\Save
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class SaveTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Save
     */
    private $controller;

    /**
     * @var Context|\PHPUnit_Framework_MockObject_MockObject
     */
    private $contextMock;

    /**
     * @var RequestInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $requestMock;

    /**
     * @var RedirectFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $resultRedirectFactoryMock;

    /**
     * @var ManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $messageManagerMock;

    /**
     * @var SalesruleRepository|\PHPUnit_Framework_MockObject_MockObject
     */
    private $salesruleRepositoryMock;

    /**
     * @var RuleRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $magentoSalesRuleRepositoryMock;

    /**
     * @var RuleInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $magentoRuleDataFactoryMock;

    /**
     * @var SalesruleConverter|\PHPUnit_Framework_MockObject_MockObject
     */
    private $salesruleConverterMock;

    /**
     * @var array
     */
    private $formData = [
        'rule_id' => '1',
        'name' => 'Test rule',
        'description' => 'rule description',
        'is_active' => '1',
        'website_ids' => [
            0 => '1'
        ],
        'customer_groups_ids' => [
            0 => '1'
        ],
        'uses_per_coupon' => '1',
        'sort_order' => '0',
        'aw_coupon_generator_expiration' => '30',
        'aw_coupongenerator_format' => CouponGenerationSpecInterface::COUPON_FORMAT_ALPHANUMERIC,
        'aw_coupon_generator_length' => '12',
        'aw_coupon_generator_prefix' => 'AAA',
        'aw_coupon_generator_suffix' => 'BBB',
        'aw_coupon_generator_dash' => '2',
        'simple_action' => RuleInterface::DISCOUNT_ACTION_BY_PERCENT,
        'discount_amount' => '10',
        'discount_qty' => '0',
        'discount_step' => '',
        'apply_to_shipping' => '1',
        'stop_rules_processing' => '1',
        'rule' => [
            'conditions' => [
                '1' => [
                    'type' => Magento\SalesRule\Model\Rule\Condition\Combine::class,
                    'aggregator' => 'all',
                    'value' => '1',
                    'new_child' => ''
                ],
                '1-1' => [
                    'type' => Magento\SalesRule\Model\Rule\Condition\Address::class,
                    'attribute' => 'base_subtotal',
                    'operator' => '>',
                    'value' => '100'
                ]
            ],
            'actions' => [
                '1' => [
                    'type' => Magento\SalesRule\Model\Rule\Condition\Product\Combine::class,
                    'aggregator' => 'all',
                    'value' => '1',
                    'new_child' => ''
                ],
                '1-1' => [
                    'type' => Magento\SalesRule\Model\Rule\Condition\Product::class,
                    'attribute' => 'category_ids',
                    'operator' => '==',
                    'value' => '3'
                ]
            ]
        ]
    ];

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->requestMock = $this->getMockForAbstractClass(
            RequestInterface::class,
            [],
            '',
            false,
            true,
            true,
            ['getPostValue']
        );
        $this->resultRedirectFactoryMock = $this->getMockBuilder(RedirectFactory::class)
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->messageManagerMock = $this->getMockForAbstractClass(ManagerInterface::class);

        $this->contextMock = $objectManager->getObject(
            Context::class,
            [
                'request' => $this->requestMock,
                'messageManager' => $this->messageManagerMock,
                'resultRedirectFactory' => $this->resultRedirectFactoryMock
            ]
        );

        $this->salesruleRepositoryMock = $this->getMockBuilder(SalesruleRepository::class)
            ->setMethods(['getByRuleId'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->magentoSalesRuleRepositoryMock = $this->getMockForAbstractClass(RuleRepositoryInterface::class);
        $this->magentoRuleDataFactoryMock = $this->getMockBuilder(RuleInterfaceFactory::class)
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->salesruleConverterMock = $this->getMockBuilder(SalesruleConverter::class)
            ->setMethods(['populateWithFormData'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->controller = $objectManager->getObject(
            Save::class,
            [
                'context' => $this->contextMock,
                'magentoRuleDataFactory' => $this->magentoRuleDataFactoryMock,
                'magentoSalesRuleRepository' => $this->magentoSalesRuleRepositoryMock,
                'salesruleRepository' => $this->salesruleRepositoryMock,
                'salesruleConverter' => $this->salesruleConverterMock
            ]
        );
    }

    /**
     * Test execute method, redirect if get data from form is empty
     */
    public function testExecuteRedirect()
    {
        $this->requestMock->expects($this->once())
            ->method('getPostValue')
            ->willReturn(null);

        $resultRedirectMock = $this->getMockBuilder(Redirect::class)
            ->setMethods(['setPath'])
            ->disableOriginalConstructor()
            ->getMock();
        $resultRedirectMock->expects($this->once())
            ->method('setPath')
            ->with('*/*/index')
            ->willReturnSelf();
        $this->resultRedirectFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($resultRedirectMock);

        $this->assertSame($resultRedirectMock, $this->controller->execute());
    }

    /**
     * Testing of execute method, redirect if error is occured
     */
    public function testExecuteRedirectError()
    {
        $exception = new \Exception;

        $this->requestMock->expects($this->once())
            ->method('getPostValue')
            ->willReturn($this->formData);

        $magentoRuleMock = $this->getMockForAbstractClass(RuleInterface::class);
        $magentoRuleMock->expects($this->once())
            ->method('setCouponType')
            ->with(RuleInterface::COUPON_TYPE_SPECIFIC_COUPON)
            ->willReturnSelf();
        $magentoRuleMock->expects($this->once())
            ->method('setUseAutoGeneration')
            ->with(true)
            ->willReturnSelf();
        $this->salesruleConverterMock->expects($this->once())
            ->method('populateWithFormData')
            ->willReturn($magentoRuleMock);
        $this->magentoSalesRuleRepositoryMock->expects($this->once())
            ->method('getById')
            ->with($this->formData['rule_id'])
            ->willReturn($magentoRuleMock);
        $this->magentoSalesRuleRepositoryMock->expects($this->once())
            ->method('save')
            ->with($magentoRuleMock)
            ->willThrowException($exception);

        $this->messageManagerMock->expects($this->once())
            ->method('addExceptionMessage')
            ->with($exception);
        $resultRedirectMock = $this->getMockBuilder(Redirect::class)
            ->setMethods(['setPath'])
            ->disableOriginalConstructor()
            ->getMock();
        $resultRedirectMock->expects($this->once())
            ->method('setPath')
            ->with('*/*/index')
            ->willReturnSelf();
        $this->resultRedirectFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($resultRedirectMock);

        $this->assertSame($resultRedirectMock, $this->controller->execute());
    }

    /**
     * Testing of execute method, successful save of edited rule
     */
    public function testExecuteSuccesfulSave()
    {
        $this->requestMock->expects($this->once())
            ->method('getPostValue')
            ->willReturn($this->formData);

        $magentoRuleMock = $this->getMockForAbstractClass(RuleInterface::class);
        $magentoRuleMock->expects($this->once())
            ->method('setCouponType')
            ->with(RuleInterface::COUPON_TYPE_SPECIFIC_COUPON)
            ->willReturnSelf();
        $magentoRuleMock->expects($this->once())
            ->method('setUseAutoGeneration')
            ->with(true)
            ->willReturnSelf();
        $this->salesruleConverterMock->expects($this->once())
            ->method('populateWithFormData')
            ->willReturn($magentoRuleMock);
        $this->magentoSalesRuleRepositoryMock->expects($this->once())
            ->method('getById')
            ->with($this->formData['rule_id'])
            ->willReturn($magentoRuleMock);
        $this->magentoSalesRuleRepositoryMock->expects($this->once())
            ->method('save')
            ->with($magentoRuleMock)
            ->willReturn($magentoRuleMock);

        $this->messageManagerMock->expects($this->once())
            ->method('addSuccessMessage')
            ->with(__('Rule was successfully saved'));
        $resultRedirectMock = $this->getMockBuilder(Redirect::class)
            ->setMethods(['setPath'])
            ->disableOriginalConstructor()
            ->getMock();
        $resultRedirectMock->expects($this->once())
            ->method('setPath')
            ->with('*/*/index')
            ->willReturnSelf();
        $this->resultRedirectFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($resultRedirectMock);

        $this->assertSame($resultRedirectMock, $this->controller->execute());
    }

    /**
     * Testing of execute method, successful save of new rule
     */
    public function testExecuteSuccesfulSaveNewRule()
    {
        unset($this->formData['rule_id']);

        $this->requestMock->expects($this->once())
            ->method('getPostValue')
            ->willReturn($this->formData);

        $magentoRuleMock = $this->getMockForAbstractClass(RuleInterface::class);
        $magentoRuleMock->expects($this->once())
            ->method('setCouponType')
            ->with(RuleInterface::COUPON_TYPE_SPECIFIC_COUPON)
            ->willReturnSelf();
        $magentoRuleMock->expects($this->once())
            ->method('setUseAutoGeneration')
            ->with(true)
            ->willReturnSelf();
        $this->salesruleConverterMock->expects($this->once())
            ->method('populateWithFormData')
            ->willReturn($magentoRuleMock);
        $this->magentoRuleDataFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($magentoRuleMock);
        $this->magentoSalesRuleRepositoryMock->expects($this->once())
            ->method('save')
            ->with($magentoRuleMock)
            ->willReturn($magentoRuleMock);

        $this->messageManagerMock->expects($this->once())
            ->method('addSuccessMessage')
            ->with(__('Rule was successfully saved'));
        $resultRedirectMock = $this->getMockBuilder(Redirect::class)
            ->setMethods(['setPath'])
            ->disableOriginalConstructor()
            ->getMock();
        $resultRedirectMock->expects($this->once())
            ->method('setPath')
            ->with('*/*/index')
            ->willReturnSelf();
        $this->resultRedirectFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($resultRedirectMock);

        $this->assertSame($resultRedirectMock, $this->controller->execute());
    }
}
