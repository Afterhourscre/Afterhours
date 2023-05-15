<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Test\Unit\Model\Rule;

use Aheadworks\OnSale\Model\Rule\Job;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;
use Aheadworks\OnSale\Model\Indexer\Rule\Processor as RuleProcessor;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class JobTest
 *
 * @package Aheadworks\OnSale\Test\Unit\Model\Rule
 */
class JobTest extends TestCase
{
    /**
     * @var Job
     */
    private $model;

    /**
     * @var RuleProcessor|\PHPUnit_Framework_MockObject_MockObject
     */
    private $ruleProcessorMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->ruleProcessorMock = $this->createPartialMock(
            RuleProcessor::class,
            ['markIndexerAsInvalid']
        );
        $this->model = $objectManager->getObject(
            Job::class,
            [
                'ruleProcessor' => $this->ruleProcessorMock
            ]
        );
    }

    /**
     * Test for applyAll method
     */
    public function testApplyAll()
    {
        $this->ruleProcessorMock->expects($this->once())->method('markIndexerAsInvalid');
        $this->model->applyAll();
    }

    /**
     * Test for applyAll method on exception
     */
    public function testExceptionApplyAll()
    {
        $exceptionMessage = 'Test exception message';
        $this->ruleProcessorMock->expects($this->once())
            ->method('markIndexerAsInvalid')
            ->willThrowException(new LocalizedException(__($exceptionMessage)));
        $this->model->applyAll();
        $this->assertEquals($exceptionMessage, $this->model->getError());
    }
}
