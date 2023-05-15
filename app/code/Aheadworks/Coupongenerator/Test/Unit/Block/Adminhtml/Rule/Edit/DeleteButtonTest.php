<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Coupongenerator\Test\Unit\Block\Adminhtml\Rule\Edit;

use Aheadworks\Coupongenerator\Block\Adminhtml\Rule\Edit\DeleteButton;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Backend\Block\Widget\Context;
use Aheadworks\Coupongenerator\Model\SalesruleRepository;
use Magento\Framework\UrlInterface;
use Magento\Framework\App\RequestInterface;
use Aheadworks\Coupongenerator\Api\Data\SalesruleInterface;

/**
 * Test for \Aheadworks\Coupongenerator\Block\Adminhtml\Rule\Edit\DeleteButton
 */
class DeleteButtonTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var DeleteButton
     */
    private $button;

    /**
     * @var Context|\PHPUnit_Framework_MockObject_MockObject
     */
    private $contextMock;

    /**
     * @var SalesruleRepository|\PHPUnit_Framework_MockObject_MockObject
     */
    private $salesruleRepositoryMock;

    /**
     * @var UrlInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $urlBuilderMock;

    /**
     * @var RequestInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $requestMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->urlBuilderMock = $this->getMockForAbstractClass(UrlInterface::class);
        $this->requestMock = $this->getMockForAbstractClass(RequestInterface::class);

        $this->salesruleRepositoryMock = $this->getMockBuilder(SalesruleRepository::class)
            ->setMethods(['get'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->contextMock = $objectManager->getObject(
            Context::class,
            [
                'urlBuilder' => $this->urlBuilderMock,
                'request' => $this->requestMock
            ]
        );

        $this->button = $objectManager->getObject(
            DeleteButton::class,
            [
                'context' => $this->contextMock,
                'salesruleRepository' => $this->salesruleRepositoryMock
            ]
        );
    }

    /**
     * Test getButtonData method
     */
    public function testGetButtonData()
    {
        $ruleId = 1;
        $deleteUrl =
            'https://ecommerce.aheadworks.com/index.php/admin/aw_coupongenerator_admin/rule/delete/id/' . $ruleId;

        $this->urlBuilderMock->expects($this->once())
            ->method('getUrl')
            ->with(
                $this->equalTo('*/*/delete'),
                $this->equalTo(['id' => $ruleId])
            )->willReturn($deleteUrl);

        $this->requestMock->expects($this->once())
            ->method('getParam')
            ->with('id')
            ->willReturn($ruleId);

        $salesruleMock = $this->getMockForAbstractClass(SalesruleInterface::class);
        $this->salesruleRepositoryMock->expects($this->once())
            ->method('get')
            ->with($ruleId)
            ->willReturn($salesruleMock);

        $this->assertTrue(is_array($this->button->getButtonData()));
    }
}
