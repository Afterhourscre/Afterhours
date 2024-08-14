<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Acr\Test\Unit\Model;

use Aheadworks\Acr\Api\Data\RuleInterface;
use Aheadworks\Acr\Model\Sender;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;
use Aheadworks\Acr\Model\RuleManagement;
use Aheadworks\Acr\Api\Data\CartHistoryInterface;
use Aheadworks\Acr\Api\Data\RuleSearchResultsInterface;
use Aheadworks\Acr\Api\Data\RuleSearchResultsInterfaceFactory;
use Aheadworks\Acr\Api\RuleRepositoryInterface;
use Aheadworks\Acr\Model\Source\Rule\Status as RuleStatus;
use Aheadworks\Acr\Model\Rule\Validator as RuleValidator;
use Aheadworks\Acr\Api\Data\PreviewInterface;
use Aheadworks\Acr\Api\Data\PreviewInterfaceFactory;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\Api\SearchCriteriaBuilder;

/**
 * Class RuleManagementTest
 * Test for \Aheadworks\Acr\Model\RuleManagement
 *
 * @package Aheadworks\Acr\Test\Unit\Model
 */
class RuleManagementTest extends TestCase
{
    /**
     * @var RuleManagement
     */
    private $model;

    /**
     * @var DateTime|\PHPUnit_Framework_MockObject_MockObject
     */
    private $dateTimeMock;

    /**
     * @var RuleSearchResultsInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $searchResultsFactoryMock;

    /**
     * @var RuleRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $ruleRepositoryMock;

    /**
     * @var RuleValidator|\PHPUnit_Framework_MockObject_MockObject
     */
    private $ruleValidatorMock;

    /**
     * @var Sender|\PHPUnit_Framework_MockObject_MockObject
     */
    private $senderMock;

    /**
     * @var PreviewInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $previewFactoryMock;

    /**
     * @var SearchCriteriaBuilder|\PHPUnit_Framework_MockObject_MockObject
     */
    private $searchCriteriaBuilderMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->dateTimeMock = $this->createPartialMock(DateTime::class, ['timestamp', 'date']);
        $this->searchResultsFactoryMock = $this->createPartialMock(
            RuleSearchResultsInterfaceFactory::class,
            ['create']
        );
        $this->ruleRepositoryMock = $this->getMockForAbstractClass(RuleRepositoryInterface::class);
        $this->ruleValidatorMock = $this->createPartialMock(RuleValidator::class, ['validate']);
        $this->senderMock = $this->createPartialMock(Sender::class, ['getTestPreview']);
        $this->previewFactoryMock = $this->createPartialMock(PreviewInterfaceFactory::class, ['create']);
        $this->searchCriteriaBuilderMock = $this->createPartialMock(
            SearchCriteriaBuilder::class,
            ['addFilter', 'create']
        );
        $this->model = $objectManager->getObject(
            RuleManagement::class,
            [
                'dateTime' => $this->dateTimeMock,
                'searchResultsFactory' => $this->searchResultsFactoryMock,
                'ruleRepository' => $this->ruleRepositoryMock,
                'ruleValidator' => $this->ruleValidatorMock,
                'sender' => $this->senderMock,
                'previewFactory' => $this->previewFactoryMock,
                'searchCriteriaBuilder' => $this->searchCriteriaBuilderMock
            ]
        );
    }

    /**
     * Test validate method
     */
    public function testValidate()
    {
        $cartData = '{}';
        $cartDataUnserialized = [];

        $cartHistoryMock = $this->getMockForAbstractClass(CartHistoryInterface::class);
        $this->searchCriteriaBuilderMock->expects($this->once())
            ->method('addFilter')
            ->with(RuleInterface::STATUS, RuleStatus::ENABLED, 'eq');
        $searchCriteria = $this->getMockForAbstractClass(SearchCriteriaInterface::class);
        $this->searchCriteriaBuilderMock->expects($this->once())
            ->method('create')
            ->willReturn($searchCriteria);

        $searchResult = $this->getMockForAbstractClass(RuleSearchResultsInterface::class);
        $this->ruleRepositoryMock->expects($this->once())
            ->method('getList')
            ->with($searchCriteria)
            ->willReturn($searchResult);

        $ruleMock = $this->getMockForAbstractClass(RuleInterface::class);
        $searchResult->expects($this->once())
            ->method('getItems')
            ->willReturn([$ruleMock]);

        $cartHistoryMock->expects($this->once())
            ->method('getCartData')
            ->willReturn($cartData);

        $this->ruleValidatorMock->expects($this->once())
            ->method('validate')
            ->with($ruleMock, $cartDataUnserialized)
            ->willReturn(true);

        $ruleSearchResults = $this->getMockForAbstractClass(RuleSearchResultsInterface::class);
        $this->searchResultsFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($ruleSearchResults);
        $ruleSearchResults->expects($this->once())
            ->method('setItems')
            ->with([$ruleMock])
            ->willReturnSelf();
        $ruleSearchResults->expects($this->once())
            ->method('setTotalCount')
            ->with(1)
            ->willReturnSelf();

        $this->assertEquals($ruleSearchResults, $this->model->validate($cartHistoryMock));
    }

    /**
     * Test getEmailSendTime method
     */
    public function testGetEmailSendTime()
    {
        $expected = 'date';
        $triggerTime = 1;
        $emailSendMinutes = 1;
        $emailSendHours = 1;
        $emailSendDays = 1;
        $sendDate = 90061;
        $ruleMock = $this->getMockForAbstractClass(RuleInterface::class);

        $this->dateTimeMock->expects($this->once())
            ->method('timestamp')
            ->with($triggerTime)
            ->willReturn($triggerTime);

        $ruleMock->expects($this->once())
            ->method('getEmailSendMinutes')
            ->willReturn($emailSendMinutes);
        $ruleMock->expects($this->once())
            ->method('getEmailSendHours')
            ->willReturn($emailSendHours);
        $ruleMock->expects($this->once())
            ->method('getEmailSendDays')
            ->willReturn($emailSendDays);

        $this->dateTimeMock->expects($this->once())
            ->method('date')
            ->with(null, $sendDate)
            ->willReturn($expected);

        $this->assertEquals($expected, $this->model->getEmailSendTime($ruleMock, $triggerTime));
    }

    /**
     * Test getPreview method
     */
    public function testGetPreview()
    {
        $storeId = 1;
        $subject = 'subject';
        $content = 'content';
        $previewContent = [
            'recipient_name' => 'recipient name',
            'recipient_email' => 'recipient email',
            'subject' => 'subject',
            'content' => 'content'
        ];

        $this->senderMock->expects($this->once())
            ->method('getTestPreview')
            ->with($storeId, $subject, $content)
            ->willReturn($previewContent);

        $previewMock = $this->getMockForAbstractClass(PreviewInterface::class);
        $this->previewFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($previewMock);

        $previewMock->expects($this->once())
            ->method('setStoreId')
            ->with($storeId)
            ->willReturnSelf();
        $previewMock->expects($this->once())
            ->method('setRecipientName')
            ->with($previewContent['recipient_name'])
            ->willReturnSelf();
        $previewMock->expects($this->once())
            ->method('setRecipientEmail')
            ->with($previewContent['recipient_email'])
            ->willReturnSelf();
        $previewMock->expects($this->once())
            ->method('setSubject')
            ->with($previewContent['subject'])
            ->willReturnSelf();
        $previewMock->expects($this->once())
            ->method('setContent')
            ->with($previewContent['content'])
            ->willReturnSelf();

        $this->assertEquals($previewMock, $this->model->getPreview($storeId, $subject, $content));
    }
}
