<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Test\Unit\Plugin\Indexer\Stock;

use Aheadworks\OnSale\Model\Indexer\Rule\Processor as RuleProcessor;
use Magento\CatalogInventory\Model\Indexer\Stock\Action\Rows as RowsAction;
use Aheadworks\OnSale\Plugin\Indexer\Stock\UpdateRowsPlugin;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Class UpdateRowsPluginTest
 *
 * @package Aheadworks\OnSale\Test\Unit\Plugin\Indexer\Stock
 */
class UpdateRowsPluginTest extends TestCase
{
    /**
     * @var UpdateRowsPlugin
     */
    private $plugin;

    /**
     * @var RuleProcessor|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $ruleProcessorMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->ruleProcessorMock = $this->createPartialMock(RuleProcessor::class, ['reindexList']);
        $this->plugin = $objectManager->getObject(
            UpdateRowsPlugin::class,
            [
                'ruleProcessor' => $this->ruleProcessorMock,
            ]
        );
    }

    /**
     * Test for aroundExecute method
     */
    public function testAroundExecute()
    {
        $productIds = [10, 15, 35];

        $closureCalled = false;
        $proceed = function ($query) use (&$closureCalled, $productIds) {
            $closureCalled = true;
            $this->assertEquals($productIds, $query);
        };

        $this->ruleProcessorMock->expects($this->once())
            ->method('reindexList')
            ->with($productIds);
        $rowsActionMock = $this->createPartialMock(RowsAction::class, []);

        $this->plugin->aroundExecute($rowsActionMock, $proceed, $productIds);
    }
}
