<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Acr\Test\Unit\Model\Source\Rule;

use Magento\Framework\Api\SearchCriteria;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\SalesRule\Api\Data\RuleSearchResultInterface;
use PHPUnit\Framework\TestCase;
use Aheadworks\Acr\Model\Source\Rule\CouponRule;
use Magento\SalesRule\Api\RuleRepositoryInterface as SalesruleRepository;
use Magento\SalesRule\Model\Data\Rule as Salesrule;
use Magento\SalesRule\Api\Data\RuleInterface as SalesruleInterface;

/**
 * Class CouponRuleTest
 * Test for \Aheadworks\Acr\Model\Source\Rule\CouponRule
 *
 * @package Aheadworks\Acr\Test\Unit\Model\Source\Rule
 */
class CouponRuleTest extends TestCase
{
    /**
     * @var CouponRule
     */
    private $model;

    /**
     * @var SalesruleRepository|\PHPUnit_Framework_MockObject_MockObject
     */
    private $salesruleRepositoryMock;

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
        $this->salesruleRepositoryMock = $this->getMockForAbstractClass(SalesruleRepository::class);
        $this->searchCriteriaBuilderMock = $this->createPartialMock(
            SearchCriteriaBuilder::class,
            ['addFilter', 'create']
        );

        $this->model = $objectManager->getObject(
            CouponRule::class,
            [
                'salesruleRepository' => $this->salesruleRepositoryMock,
                'searchCriteriaBuilder' => $this->searchCriteriaBuilderMock
            ]
        );
    }

    /**
     * Test toOptionArray method
     */
    public function testToOptionArray()
    {
        $ruleId = 1;
        $ruleName = 'Name';
        $searchCriteriaMock = $this->getMockForAbstractClass(SearchCriteria::class);
        $salesRuleSearchResultsMock = $this->getMockForAbstractClass(RuleSearchResultInterface::class);
        $salesRuleMock = $this->getMockForAbstractClass(SalesruleInterface::class);

        $this->searchCriteriaBuilderMock->expects($this->exactly(2))
            ->method('addFilter')
            ->withConsecutive(
                [Salesrule::KEY_USE_AUTO_GENERATION, true],
                [Salesrule::KEY_IS_ACTIVE, true]
            )->willReturnSelf();
        $this->searchCriteriaBuilderMock->expects($this->once())
            ->method('create')
            ->willReturn($searchCriteriaMock);
        $this->salesruleRepositoryMock->expects($this->once())
            ->method('getList')
            ->with($searchCriteriaMock)
            ->willReturn($salesRuleSearchResultsMock);

        $salesRuleSearchResultsMock->expects($this->once())
            ->method('getItems')
            ->willReturn([$salesRuleMock]);

        $salesRuleMock->expects($this->once())
            ->method('getRuleId')
            ->willReturn($ruleId);
        $salesRuleMock->expects($this->once())
            ->method('getName')
            ->willReturn($ruleName);

        $this->assertTrue(is_array($this->model->toOptionArray()));
    }
}
