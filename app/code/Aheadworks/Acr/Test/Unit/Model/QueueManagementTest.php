<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Acr\Test\Unit\Model;

use Aheadworks\Acr\Model\Sender;
use Magento\Framework\Api\SearchCriteria;
use Magento\Framework\Exception\MailException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;
use Aheadworks\Acr\Model\QueueManagement;
use Aheadworks\Acr\Api\Data\QueueInterface;
use Aheadworks\Acr\Api\Data\QueueInterfaceFactory;
use Aheadworks\Acr\Api\Data\QueueSearchResultsInterface;
use Aheadworks\Acr\Api\Data\CartHistoryInterface;
use Aheadworks\Acr\Api\CartHistoryRepositoryInterface;
use Aheadworks\Acr\Api\Data\RuleInterface;
use Aheadworks\Acr\Api\RuleManagementInterface;
use Aheadworks\Acr\Api\RuleRepositoryInterface;
use Aheadworks\Acr\Api\QueueRepositoryInterface;
use Aheadworks\Acr\Api\Data\PreviewInterface;
use Aheadworks\Acr\Api\Data\PreviewInterfaceFactory;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Store\Model\App\Emulation as AppEmulation;
use Aheadworks\Acr\Model\Template\PreviewFilter;

/**
 * Class QueueManagementTest
 * Test for \Aheadworks\Acr\Model\QueueManagement
 *
 * @package Aheadworks\Acr\Test\Unit\Model
 */
class QueueManagementTest extends TestCase
{
    /**
     * @var QueueManagement
     */
    private $model;

    /**
     * @var QueueInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $queueFactoryMock;

    /**
     * @var QueueRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $queueRepositoryMock;

    /**
     * @var CartHistoryRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $cartHistoryRepositoryMock;

    /**
     * @var RuleManagementInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $ruleManagementMock;

    /**
     * @var RuleRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $ruleRepositoryMock;

    /**
     * @var PreviewInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $previewFactoryMock;

    /**
     * @var Sender|\PHPUnit_Framework_MockObject_MockObject
     */
    private $senderMock;

    /**
     * @var SearchCriteriaBuilder|\PHPUnit_Framework_MockObject_MockObject
     */
    private $searchCriteriaBuilderMock;

    /**
     * @var DateTime|\PHPUnit_Framework_MockObject_MockObject
     */
    private $dateTimeMock;

    /**
     * @var AppEmulation|\PHPUnit_Framework_MockObject_MockObject
     */
    private $appEmulationMock;

    /**
     * @var PreviewFilter|\PHPUnit_Framework_MockObject_MockObject
     */
    private $previewFilterMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->queueFactoryMock = $this->createPartialMock(QueueInterfaceFactory::class, ['create']);
        $this->queueRepositoryMock = $this->getMockForAbstractClass(QueueRepositoryInterface::class);
        $this->cartHistoryRepositoryMock = $this->getMockForAbstractClass(CartHistoryRepositoryInterface::class);
        $this->ruleManagementMock = $this->getMockForAbstractClass(RuleManagementInterface::class);
        $this->ruleRepositoryMock = $this->getMockForAbstractClass(RuleRepositoryInterface::class);
        $this->previewFilterMock = $this->createMock(PreviewFilter::class);
        $this->previewFactoryMock = $this->createPartialMock(PreviewInterfaceFactory::class, ['create']);
        $this->senderMock = $this->createPartialMock(Sender::class, ['sendQueueItem', 'sendTestEmail', 'getPreview']);
        $this->searchCriteriaBuilderMock = $this->createPartialMock(
            SearchCriteriaBuilder::class,
            ['addFilter', 'create']
        );
        $this->dateTimeMock = $this->createPartialMock(DateTime::class, ['date', 'timestamp']);
        $this->appEmulationMock = $this->createPartialMock(
            AppEmulation::class,
            ['startEnvironmentEmulation', 'stopEnvironmentEmulation']
        );

        $this->model = $objectManager->getObject(
            QueueManagement::class,
            [
                'queueFactory' => $this->queueFactoryMock,
                'queueRepository' => $this->queueRepositoryMock,
                'cartHistoryRepository' => $this->cartHistoryRepositoryMock,
                'ruleManagement' => $this->ruleManagementMock,
                'ruleRepository' => $this->ruleRepositoryMock,
                'previewFactory' => $this->previewFactoryMock,
                'sender' => $this->senderMock,
                'searchCriteriaBuilder' => $this->searchCriteriaBuilderMock,
                'dateTime' => $this->dateTimeMock,
                'appEmulation' => $this->appEmulationMock,
                'previewFilter' => $this->previewFilterMock
            ]
        );
    }

    /**
     * Test add method
     */
    public function testAdd()
    {
        $expected = true;
        $ruleMock = $this->getMockForAbstractClass(RuleInterface::class);
        $cartHistoryMock = $this->getMockForAbstractClass(CartHistoryInterface::class);

        $queueMock = $this->initialAddMethod($ruleMock, $cartHistoryMock);
        $this->queueRepositoryMock->expects($this->once())
            ->method('save')
            ->with($queueMock);

        $this->assertEquals($expected, $this->model->add($ruleMock, $cartHistoryMock));
    }

    /**
     * Test add method, on exception
     */
    public function testAddOnException()
    {
        $expected = false;
        $ruleMock = $this->getMockForAbstractClass(RuleInterface::class);
        $cartHistoryMock = $this->getMockForAbstractClass(CartHistoryInterface::class);

        $queueMock = $this->initialAddMethod($ruleMock, $cartHistoryMock);
        $this->queueRepositoryMock->expects($this->once())
            ->method('save')
            ->with($queueMock)
            ->willThrowException(new \Exception('Exception'));

        $this->assertEquals($expected, $this->model->add($ruleMock, $cartHistoryMock));
    }

    /**
     * Test cancel method
     *
     * @param int $status
     * @param bool $expected
     * @dataProvider cancelDataProvider
     */
    public function testCancel($status, $expected)
    {
        $queueMock = $this->getMockForAbstractClass(QueueInterface::class);
        $queueMock->expects($this->once())
            ->method('getStatus')
            ->willReturn($status);

        if ($status == QueueInterface::STATUS_PENDING) {
            $queueMock->expects($this->once())
                ->method('setStatus')
                ->with(QueueInterface::STATUS_CANCELLED)
                ->willReturnSelf();
            $this->queueRepositoryMock->expects($this->once())
                ->method('save')
                ->with($queueMock)
                ->willReturnSelf();
        }
        
        $this->assertEquals($expected, $this->model->cancel($queueMock));
    }

    /**
     * Test cancelById method
     *
     * @param int $status
     * @param bool $expected
     * @dataProvider cancelDataProvider
     */
    public function testCancelById($status, $expected)
    {
        $queueId = 1;
        $queueMock = $this->getMockForAbstractClass(QueueInterface::class);
        $this->queueRepositoryMock->expects($this->once())
            ->method('get')
            ->with($queueId)
            ->willReturn($queueMock);
        $queueMock->expects($this->once())
            ->method('getStatus')
            ->willReturn($status);

        if ($status == QueueInterface::STATUS_PENDING) {
            $queueMock->expects($this->once())
                ->method('setStatus')
                ->with(QueueInterface::STATUS_CANCELLED)
                ->willReturnSelf();
            $this->queueRepositoryMock->expects($this->once())
                ->method('save')
                ->with($queueMock)
                ->willReturnSelf();
        }

        $this->assertEquals($expected, $this->model->cancelById($queueId));
    }

    /**
     * Test cancelById method, on exception
     *
     * @expectedException \Magento\Framework\Exception\LocalizedException
     * @expectedExceptionMessage Unable to cancel.
     */
    public function testCancelByIdOnException()
    {
        $queueId = 1;
        $this->queueRepositoryMock->expects($this->once())
            ->method('get')
            ->with($queueId)
            ->willThrowException(new NoSuchEntityException(__('Exception')));

        $this->model->cancelById($queueId);
    }

    /**
     * Test send method
     */
    public function testSend()
    {
        $expected = true;
        $queueMock = $this->send();

        $this->assertEquals($expected, $this->model->send($queueMock));
    }

    /**
     * Test send method, on exception
     */
    public function testSendOnException()
    {
        $expected = false;
        $storeId = 1;
        $queueMock = $this->getMockForAbstractClass(QueueInterface::class);

        $queueMock->expects($this->once())
            ->method('getStoreId')
            ->willReturn($storeId);
        $this->appEmulationMock->expects($this->once())
            ->method('startEnvironmentEmulation')
            ->with($storeId, 'frontend', true);
        $this->senderMock->expects($this->once())
            ->method('sendQueueItem')
            ->with($queueMock)
            ->willThrowException(new MailException(__('Exception')));
        $this->appEmulationMock->expects($this->once())
            ->method('stopEnvironmentEmulation');

        $queueMock->expects($this->once())
            ->method('setStatus')
            ->with(QueueInterface::STATUS_FAILED)
            ->willReturnSelf();

        $this->queueRepositoryMock->expects($this->once())
            ->method('save')
            ->with($queueMock);

        $this->assertEquals($expected, $this->model->send($queueMock));
    }

    /**
     * Test sendTest method
     */
    public function testSendTest()
    {
        $expected = true;
        $storeIds = [1, 2];
        $ruleId = 1;
        $date = 'date';
        $ruleMock = $this->getMockForAbstractClass(RuleInterface::class);

        $ruleMock->expects($this->once())
            ->method('getStoreIds')
            ->willReturn($storeIds);

        $queueMock = $this->getMockForAbstractClass(QueueInterface::class);
        $this->queueFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($queueMock);
        $ruleMock->expects($this->once())
            ->method('getId')
            ->willReturn($ruleId);
        $queueMock->expects($this->once())
            ->method('setRuleId')
            ->with($ruleId)
            ->willReturnSelf();
        $queueMock->expects($this->once())
            ->method('setStoreId')
            ->with($storeIds[0])
            ->willReturnSelf();

        $this->senderMock->expects($this->once())
            ->method('sendTestEmail')
            ->with($queueMock)
            ->willReturn($queueMock);

        $this->dateTimeMock->expects($this->once())
            ->method('date')
            ->willReturn($date);

        $queueMock->expects($this->once())
            ->method('setStatus')
            ->with(QueueInterface::STATUS_SENT)
            ->willReturnSelf();
        $queueMock->expects($this->once())
            ->method('setScheduledAt')
            ->with($date)
            ->willReturnSelf();
        $queueMock->expects($this->once())
            ->method('setSentAt')
            ->with($date)
            ->willReturnSelf();
        $queueMock->expects($this->once())
            ->method('setCartHistoryId')
            ->with(0)
            ->willReturnSelf();

        $this->queueRepositoryMock->expects($this->once())
            ->method('save')
            ->with($queueMock);

        $this->assertEquals($expected, $this->model->sendTest($ruleMock));
    }

    /**
     * Test sendTest method, on exception
     */
    public function testSendTestOnException()
    {
        $expected = false;
        $storeIds = [1, 2];
        $ruleId = 1;
        $ruleMock = $this->getMockForAbstractClass(RuleInterface::class);

        $ruleMock->expects($this->once())
            ->method('getStoreIds')
            ->willReturn($storeIds);

        $queueMock = $this->getMockForAbstractClass(QueueInterface::class);
        $this->queueFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($queueMock);
        $ruleMock->expects($this->once())
            ->method('getId')
            ->willReturn($ruleId);
        $queueMock->expects($this->once())
            ->method('setRuleId')
            ->with($ruleId)
            ->willReturnSelf();
        $queueMock->expects($this->once())
            ->method('setStoreId')
            ->with($storeIds[0])
            ->willReturnSelf();

        $this->senderMock->expects($this->once())
            ->method('sendTestEmail')
            ->with($queueMock)
            ->willThrowException(new MailException(__('Exception')));

        $queueMock->expects($this->once())
            ->method('setStatus')
            ->with(QueueInterface::STATUS_FAILED)
            ->willReturnSelf();

        $this->queueRepositoryMock->expects($this->once())
            ->method('save')
            ->with($queueMock);

        $this->assertEquals($expected, $this->model->sendTest($ruleMock));
    }

    /**
     * Test sendById method
     */
    public function testSendById()
    {
        $expected = true;
        $queueId = 1;
        $queueMock = $this->send();

        $this->queueRepositoryMock->expects($this->once())
            ->method('get')
            ->with($queueId)
            ->willReturn($queueMock);

        $this->assertEquals($expected, $this->model->sendById($queueId));
    }

    /**
     * Test sendById method, on exception
     *
     * @expectedException \Magento\Framework\Exception\LocalizedException
     * @expectedExceptionMessage Unable to send.
     */
    public function testSendByIdOnException()
    {
        $queueId = 1;

        $this->queueRepositoryMock->expects($this->once())
            ->method('get')
            ->with($queueId)
            ->willThrowException(new NoSuchEntityException(__('Exception')));

        $this->model->sendById($queueId);
    }

    /**
     * Test getPreview method
     *
     * @param array $queueData
     * @dataProvider getPreviewDataProvider
     */
    public function testGetPreview($queueData)
    {
        $queueMock = $this->getMockForAbstractClass(QueueInterface::class);
        $previewMock = $this->getMockForAbstractClass(PreviewInterface::class);
        $this->previewFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($previewMock);
        $queueMock->expects($this->once())
            ->method('getStoreId')
            ->willReturn($queueData['store_id']);
        $queueMock->expects($this->once())
            ->method('getRecipientName')
            ->willReturn($queueData['recipient_name']);
        $queueMock->expects($this->once())
            ->method('getRecipientEmail')
            ->willReturn($queueData['recipient_email']);

        $previewMock->expects($this->once())
            ->method('setStoreId')
            ->with($queueData['store_id'])
            ->willReturnSelf();
        $previewMock->expects($this->once())
            ->method('setRecipientName')
            ->with($queueData['recipient_name'])
            ->willReturnSelf();
        $previewMock->expects($this->once())
            ->method('setRecipientEmail')
            ->with($queueData['recipient_email'])
            ->willReturnSelf();

        $queueMock->expects($this->atLeastOnce())
            ->method('getSavedContent')
            ->willReturn($queueData['saved_content']);

        if ($queueData['saved_content']) {
            $queueMock->expects($this->once())
                ->method('getSavedSubject')
                ->willReturn($queueData['saved_subject']);
            $previewMock->expects($this->once())
                ->method('setSubject')
                ->with($queueData['saved_subject'])
                ->willReturnSelf();
            $previewMock->expects($this->once())
                ->method('setContent')
                ->with($queueData['saved_content'])
                ->willReturnSelf();
        } else {
            $ruleData = [
                'subject' => 'subject',
                'content' => 'content'
            ];
            $ruleMock = $this->getMockForAbstractClass(RuleInterface::class);
            $this->ruleRepositoryMock->expects($this->once())
                ->method('get')
                ->with($queueData['rule_id'])
                ->willReturn($ruleMock);

            $ruleMock->expects($this->once())
                ->method('getSubject')
                ->willReturn($ruleData['subject']);
            $ruleMock->expects($this->once())
                ->method('getContent')
                ->willReturn($ruleData['content']);

            $this->senderMock->expects($this->once())
                ->method('getPreview')
                ->with($queueMock, $ruleData['subject'], $ruleData['content'])
                ->willReturn($ruleData);
            $previewMock->expects($this->once())
                ->method('setSubject')
                ->with($ruleData['subject'])
                ->willReturnSelf();
            $this->previewFilterMock->expects($this->once())
                ->method('filter')
                ->with($ruleData['content'])
                ->willReturn($ruleData['content']);
            $previewMock->expects($this->once())
                ->method('setContent')
                ->with($ruleData['content'])
                ->willReturnSelf();
        }

        $this->assertEquals($previewMock, $this->model->getPreview($queueMock));
    }

    /**
     * Test clearQueue method
     *
     * @param array $keepForDays
     * @param bool $expected
     * @dataProvider clearQueueDataProvider
     */
    public function testClearQueue($keepForDays, $expected)
    {
        if ($keepForDays) {
            $searchCriteriaMock = $this->getMockForAbstractClass(SearchCriteria::class);
            $queueSearchResultsMock = $this->getMockForAbstractClass(QueueSearchResultsInterface::class);
            $queueMock = $this->getMockForAbstractClass(QueueInterface::class);

            $this->searchCriteriaBuilderMock->expects($this->once())
                ->method('addFilter');
            $this->searchCriteriaBuilderMock->expects($this->once())
                ->method('create')
                ->willReturn($searchCriteriaMock);
            $this->queueRepositoryMock->expects($this->once())
                ->method('getList')
                ->with($searchCriteriaMock)
                ->willReturn($queueSearchResultsMock);

            $queueSearchResultsMock->expects($this->once())
                ->method('getItems')
                ->willReturn([$queueMock]);

            $this->queueRepositoryMock->expects($this->once())
                ->method('delete')
                ->with($queueMock);
        }

        $this->assertEquals($expected, $this->model->clearQueue($keepForDays));
    }

    /**
     * Test sendScheduledEmails method
     */
    public function testSendScheduledEmails()
    {
        $expected = true;
        $timestamp = 1;
        $searchCriteriaMock = $this->getMockForAbstractClass(SearchCriteria::class);
        $queueSearchResultsMock = $this->getMockForAbstractClass(QueueSearchResultsInterface::class);

        $this->dateTimeMock->expects($this->once())
            ->method('timestamp')
            ->willReturn($timestamp);
        $this->searchCriteriaBuilderMock->expects($this->exactly(2))
            ->method('addFilter')
            ->willReturnSelf();
        $this->searchCriteriaBuilderMock->expects($this->once())
            ->method('create')
            ->willReturn($searchCriteriaMock);
        $this->queueRepositoryMock->expects($this->once())
            ->method('getList')
            ->with($searchCriteriaMock)
            ->willReturn($queueSearchResultsMock);

        $queueMock = $this->send();
        $queueSearchResultsMock->expects($this->once())
            ->method('getItems')
            ->willReturn([$queueMock]);

        $this->assertEquals($expected, $this->model->sendScheduledEmails());
    }

    /**
     * Data provider for cancel method
     *
     * @return array
     */
    public function cancelDataProvider()
    {
        return [[QueueInterface::STATUS_PENDING, true], [QueueInterface::STATUS_CANCELLED, false]];
    }

    /**
     * Data provider for getPreview method
     *
     * @return array
     */
    public function getPreviewDataProvider()
    {
        return [
            [
                'store_id' => 1,
                'recipient_name' => 'recipient name',
                'recipient_email' => 'email@example.com',
                'saved_subject' => null
            ],
            [
                'store_id' => 1,
                'recipient_name' => 'recipient name',
                'recipient_email' => 'email@example.com',
                'saved_subject' => 'subject',
                'saved_content' => 'content',
                'rule_id' => 1
            ]
        ];
    }

    /**
     * Data provider for clearQueue method
     *
     * @return array
     */
    public function clearQueueDataProvider()
    {
        return [[10, true], [0, false]];
    }

    /**
     * Initial add method
     *
     * @param RuleInterface|\PHPUnit_Framework_MockObject_MockObject $ruleMock
     * @param CartHistoryInterface|\PHPUnit_Framework_MockObject_MockObject $cartHistoryMock
     * @return QueueInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private function initialAddMethod($ruleMock, $cartHistoryMock)
    {
        $cartDataUnserialized = [
            'store_id' => 1,
            'customer_name' => 'customer name',
            'email' => 'email@example.com'
        ];
        $ruleId = 1;
        $cartHistoryId = 1;
        $date = 'date';
        $scheduledAt = 'scheduled time';
        $cartData = json_encode($cartDataUnserialized);

        $cartHistoryMock->expects($this->once())
            ->method('getCartData')
            ->willReturn($cartData);
        $queueMock = $this->getMockForAbstractClass(QueueInterface::class);
        $this->queueFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($queueMock);

        $ruleMock->expects($this->once())
            ->method('getId')
            ->willReturn($ruleId);

        $queueMock->expects($this->once())
            ->method('setRuleId')
            ->with($ruleId)
            ->willReturnSelf();
        $queueMock->expects($this->once())
            ->method('setStatus')
            ->with(QueueInterface::STATUS_PENDING)
            ->willReturnSelf();

        $this->dateTimeMock->expects($this->once())
            ->method('date')
            ->willReturn($date);
        $this->ruleManagementMock->expects($this->once())
            ->method('getEmailSendTime')
            ->with($ruleMock, $date)
            ->willReturn($scheduledAt);
        $queueMock->expects($this->once())
            ->method('setScheduledAt')
            ->with($scheduledAt)
            ->willReturnSelf();
        $queueMock->expects($this->once())
            ->method('setStoreId')
            ->with($cartDataUnserialized['store_id'])
            ->willReturnSelf();
        $queueMock->expects($this->once())
            ->method('setRecipientName')
            ->with($cartDataUnserialized['customer_name'])
            ->willReturnSelf();
        $queueMock->expects($this->once())
            ->method('setRecipientEmail')
            ->with($cartDataUnserialized['email'])
            ->willReturnSelf();

        $cartHistoryMock->expects($this->once())
            ->method('getId')
            ->willReturn($cartHistoryId);
        $queueMock->expects($this->once())
            ->method('setCartHistoryId')
            ->with($cartHistoryId)
            ->willReturnSelf();

        return $queueMock;
    }

    /**
     * Send
     *
     * @return QueueInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private function send()
    {
        $storeId = 1;
        $date = 'date';
        $queueMock = $this->getMockForAbstractClass(QueueInterface::class);

        $queueMock->expects($this->once())
            ->method('getStoreId')
            ->willReturn($storeId);
        $this->appEmulationMock->expects($this->once())
            ->method('startEnvironmentEmulation')
            ->with($storeId, 'frontend', true);
        $this->senderMock->expects($this->once())
            ->method('sendQueueItem')
            ->with($queueMock)
            ->willReturn($queueMock);
        $this->appEmulationMock->expects($this->once())
            ->method('stopEnvironmentEmulation');

        $queueMock->expects($this->once())
            ->method('setStatus')
            ->with(QueueInterface::STATUS_SENT)
            ->willReturnSelf();
        $this->dateTimeMock->expects($this->once())
            ->method('date')
            ->willReturn($date);
        $queueMock->expects($this->once())
            ->method('setSentAt')
            ->with($date)
            ->willReturnSelf();

        $this->queueRepositoryMock->expects($this->once())
            ->method('save')
            ->with($queueMock);

        return $queueMock;
    }
}
