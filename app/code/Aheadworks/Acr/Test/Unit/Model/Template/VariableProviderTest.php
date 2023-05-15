<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Acr\Test\Unit\Model\Template;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Aheadworks\Acr\Model\Source\Email\Variables;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\Acr\Model\Template\VariableProvider;
use Aheadworks\Acr\Api\CartHistoryRepositoryInterface;
use Aheadworks\Acr\Api\Data\CartHistoryInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Aheadworks\Acr\Model\Template\VariableProcessor;
use Aheadworks\Acr\Api\Data\QueueInterface;

/**
 * Class CartRestorerTest
 * @package Aheadworks\Acr\Test\Unit\Model
 */
class VariableProviderTest extends TestCase
{
    /**
     * @var VariableProvider
     */
    private $variableProvider;

    /**
     * @var CartHistoryRepositoryInterface
     */
    private $cartHistoryRepositoryMock;

    /**
     * @var CartRepositoryInterface
     */
    private $cartRepositoryMock;

    /**
     * @var VariableProcessor
     */
    private $variableProcessorMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->cartHistoryRepositoryMock = $this->createMock(CartHistoryRepositoryInterface::class);
        $this->cartRepositoryMock = $this->createMock(CartRepositoryInterface::class);
        $this->variableProcessorMock = $this->createMock(VariableProcessor::class);
        $this->variableProvider = $objectManager->getObject(
            VariableProvider::class,
            [
                'cartHistoryRepository' => $this->cartHistoryRepositoryMock,
                'cartRepository' => $this->cartRepositoryMock,
                'variableProcessor' => $this->variableProcessorMock,
            ]
        );
    }

    /**
     * Test getTemplateVarsData method
     *
     * @param array $expected
     * @dataProvider getTemplateVarsDataDataProvider
     */
    public function testGetTemplateVarsData($dataProvider)
    {
        $queueItem = $this->createMock(QueueInterface::class);
        $historyItem = $this->createMock(CartHistoryInterface::class);
        $quote = $this->createMock(CartRepositoryInterface::class);
        $id = '1';
        $storeId = '1';
        $ruleId = '1';
        $cartData = json_encode(['customer_name' => 'test', 'entity_id' => '1']);
        $cartData2 = json_decode(json_encode(['customer_name' => 'test', 'entity_id' => '1']), true);
        $data = [
            'store_id' => $storeId,
            'rule_id' => $ruleId,
            'customer_name' => $cartData2['customer_name'],
            'variables' => [
                Variables::QUOTE,
                Variables::CUSTOMER,
                Variables::STORE,
                Variables::CART_RESTORE_LINK
            ]
        ];
        $result = ['1', '2', '3'];
        if ($dataProvider) {
            $queueItem->expects($this->once())
                ->method('getCartHistoryId')
                ->willReturn($id)
                ->willThrowException(new NoSuchEntityException());

            $this->assertSame([], $this->variableProvider->getTemplateVarsData($queueItem));
        } else {
            $queueItem->expects($this->once())
                ->method('getCartHistoryId')
                ->willReturn($id);
            $this->cartHistoryRepositoryMock->expects($this->once())
                ->method('get')
                ->with($id)
                ->willReturn($historyItem);
            $historyItem->expects($this->once())
                ->method('getCartData')
                ->willReturn($cartData);
            $this->cartRepositoryMock->expects($this->once())
                ->method('get')
                ->with($cartData2['entity_id'])
                ->willReturnSelf();
            $queueItem->expects($this->once())
                ->method('getStoreId')
                ->willReturn($storeId);
            $queueItem->expects($this->once())
                ->method('getRuleId')
                ->willReturn($ruleId);
            $this->variableProcessorMock->expects($this->once())
                ->method('processTemplateVariable')
                ->with($quote, $data)
                ->willReturn($result);
            $result = array_merge($cartData2, $result);

            $this->assertSame($result, $this->variableProvider->getTemplateVarsData($queueItem));
        }
    }

    /**
     * Test getTemplateVarsData method on exception
     *
     * @param bool $expected
     */
    public function testGetTemplateVarsDataOnException()
    {
        $queueItem = $this->createMock(QueueInterface::class);
        $historyItem = $this->createMock(CartHistoryInterface::class);
        $id = '1';
        $cartData = json_encode(['customer_name' => 'test', 'entity_id' => '1']);
        $cartData2 = json_decode(json_encode(['customer_name' => 'test', 'entity_id' => '1']), true);

        $queueItem->expects($this->once())
            ->method('getCartHistoryId')
            ->willReturn($id);
        $this->cartHistoryRepositoryMock->expects($this->once())
            ->method('get')
            ->with($id)
            ->willReturn($historyItem);
        $historyItem->expects($this->once())
            ->method('getCartData')
            ->willReturn($cartData);
        $this->cartRepositoryMock->expects($this->once())
            ->method('get')
            ->with($cartData2['entity_id'])
            ->willReturnSelf()
            ->willThrowException(new NoSuchEntityException(__('Exception')));

        $this->expectException(LocalizedException::class);
        $this->expectExceptionMessage('Event object is missing');

        $this->variableProvider->getTemplateVarsData($queueItem);
    }

    /**
     * Test getTestTemplateVarsData method
     */
    public function testGetTestTemplateVarsData()
    {
        $storeId = '1';
        $data = ['store_id' => $storeId,
                'variables' => [
                    Variables::QUOTE,
                    Variables::CUSTOMER,
                    Variables::STORE,
                    Variables::COUPON,
                    Variables::QUOTE_DATA,
                    Variables::CUSTOMER_DATA,
                    Variables::CART_RESTORE_LINK
                ]];
        $result = ['1', '2', '3', '4', '5'];

        $this->variableProcessorMock->expects($this->once())
            ->method('processTemplateVariableTest')
            ->with($data)
            ->willReturn($result);
        $this->assertSame($result, $this->variableProvider->getTestTemplateVarsData($storeId));
    }

    /**
     * Data provider for getTemplateVarsData method
     *
     * @return array
     */
    public function getTemplateVarsDataDataProvider()
    {
        return [
            [true],
            [false]
        ];
    }
}
