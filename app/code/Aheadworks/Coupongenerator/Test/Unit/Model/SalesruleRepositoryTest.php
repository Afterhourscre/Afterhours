<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Coupongenerator\Test\Unit\Model;

use Aheadworks\Coupongenerator\Model\SalesruleRepository;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\EntityManager\EntityManager;
use Magento\Framework\Api\DataObjectHelper;
use Aheadworks\Coupongenerator\Api\Data\SalesruleInterfaceFactory;
use Aheadworks\Coupongenerator\Api\Data\SalesruleInterface;
use Aheadworks\Coupongenerator\Model\SalesruleRegistry;
use Aheadworks\Coupongenerator\Model\ResourceModel\SalesruleFactory;
use Aheadworks\Coupongenerator\Model\ResourceModel\Salesrule as SalesruleResource;

/**
 * Test for \Aheadworks\Coupongenerator\Model\SalesruleRepository
 */
class SalesruleRepositoryTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var SalesruleRepository
     */
    private $model;

    /**
     * @var EntityManager|\PHPUnit_Framework_MockObject_MockObject
     */
    private $entityManagerMock;

    /**
     * @var DataObjectHelper|\PHPUnit_Framework_MockObject_MockObject
     */
    private $dataObjectHelperMock;

    /**
     * @var SalesruleRegistry|\PHPUnit_Framework_MockObject_MockObject
     */
    private $salesruleRegistryMock;

    /**
     * @var SalesruleInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $salesruleInterfaceFactoryMock;

    /**
     * @var SalesruleFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $salesruleResourceFactoryMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->entityManagerMock = $this->getMockBuilder(EntityManager::class)
            ->setMethods(['load', 'delete', 'save'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->dataObjectHelperMock = $this->getMockBuilder(DataObjectHelper::class)
            ->setMethods([])
            ->disableOriginalConstructor()
            ->getMock();
        $this->salesruleRegistryMock = $this->getMockBuilder(SalesruleRegistry::class)
            ->setMethods(['retrieve', 'retrieveByRuleId', 'remove', 'removeByRuleId', 'push'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->salesruleInterfaceFactoryMock = $this->getMockBuilder(SalesruleInterfaceFactory::class)
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->salesruleResourceFactoryMock = $this->getMockBuilder(SalesruleFactory::class)
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->model = $objectManager->getObject(
            SalesruleRepository::class,
            [
                'entityManager' => $this->entityManagerMock,
                'dataObjectHelper' => $this->dataObjectHelperMock,
                'salesruleRegistry' => $this->salesruleRegistryMock,
                'salesruleInterfaceFactory' => $this->salesruleInterfaceFactoryMock,
                'salesruleResourceFactory' => $this->salesruleResourceFactoryMock
            ]
        );
    }

    /**
     * Test get method
     */
    public function testGet()
    {
        $salesruleId = 1;

        $this->salesruleRegistryMock->expects($this->once())
            ->method('retrieve')
            ->with($salesruleId)
            ->willReturn(null);

        $salesruleMock = $this->getMockForAbstractClass(SalesruleInterface::class);
        $salesruleMock->expects($this->atLeastOnce())
            ->method('getId')
            ->willReturn($salesruleId);
        $this->salesruleInterfaceFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($salesruleMock);

        $this->entityManagerMock->expects($this->once())
            ->method('load')
            ->with($salesruleMock, $salesruleId);

        $this->salesruleRegistryMock->expects($this->once())
            ->method('push')
            ->with($salesruleMock);

        $this->assertSame($salesruleMock, $this->model->get($salesruleId));
    }

    /**
     * Test get method, that proper exception is thrown if salesrule not exist
     *
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     * @expectedExceptionMessage No such entity with id = 1
     */
    public function testGetOnExeption()
    {
        $salesruleId = 1;

        $this->salesruleRegistryMock->expects($this->once())
            ->method('retrieve')
            ->with($salesruleId)
            ->willReturn(null);

        $salesruleMock = $this->getMockForAbstractClass(SalesruleInterface::class);
        $salesruleMock->expects($this->atLeastOnce())
            ->method('getId')
            ->willReturn(null);
        $this->salesruleInterfaceFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($salesruleMock);

        $this->assertSame($salesruleMock, $this->model->get($salesruleId));
    }

    /**
     * Test getByRuleId method
     */
    public function testGetByRuleId()
    {
        $salesruleId = 1;
        $ruleId = 2;
        $ruleData = [
            'id' => $salesruleId
        ];

        $this->salesruleRegistryMock->expects($this->once())
            ->method('retrieveByRuleId')
            ->with($ruleId)
            ->willReturn(null);

        $salesruleResourceMock = $this->getMockBuilder(SalesruleResource::class)
            ->setMethods(['getByRuleId'])
            ->disableOriginalConstructor()
            ->getMock();
        $salesruleResourceMock->expects($this->once())
            ->method('getByRuleId')
            ->with($ruleId)
            ->willReturn($ruleData);
        $this->salesruleResourceFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($salesruleResourceMock);

        $salesruleMock = $this->getMockForAbstractClass(SalesruleInterface::class);
        $this->salesruleInterfaceFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($salesruleMock);

        $this->dataObjectHelperMock->expects($this->once())
            ->method('populateWithArray')
            ->with($salesruleMock, $ruleData, SalesruleInterface::class);

        $this->salesruleRegistryMock->expects($this->once())
            ->method('push')
            ->with($salesruleMock);

        $this->assertSame($salesruleMock, $this->model->getByRuleId($ruleId));
    }

    /**
     * Test getByRuleId method, that proper exception is thrown if salesrule not exist
     *
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     * @expectedExceptionMessage No such entity with rule_id = 2
     */
    public function testGetByRuleIdOnException()
    {
        $ruleId = 2;

        $this->salesruleRegistryMock->expects($this->once())
            ->method('retrieveByRuleId')
            ->with($ruleId)
            ->willReturn(null);

        $salesruleResourceMock = $this->getMockBuilder(SalesruleResource::class)
            ->setMethods(['getByRuleId'])
            ->disableOriginalConstructor()
            ->getMock();
        $salesruleResourceMock->expects($this->once())
            ->method('getByRuleId')
            ->with($ruleId)
            ->willReturn(null);
        $this->salesruleResourceFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($salesruleResourceMock);

        $salesruleMock = $this->getMockForAbstractClass(SalesruleInterface::class);

        $this->assertSame($salesruleMock, $this->model->getByRuleId($ruleId));
    }

    /**
     * Test delete method
     */
    public function testDelete()
    {
        $salesruleId = 1;

        $this->salesruleRegistryMock->expects($this->once())
            ->method('retrieve')
            ->with($salesruleId)
            ->willReturn(null);

        $salesruleMock = $this->getMockForAbstractClass(SalesruleInterface::class);
        $salesruleMock->expects($this->atLeastOnce())
            ->method('getId')
            ->willReturn($salesruleId);
        $this->salesruleInterfaceFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($salesruleMock);

        $this->entityManagerMock->expects($this->once())
            ->method('load')
            ->with($salesruleMock, $salesruleId);
        $this->entityManagerMock->expects($this->once())
            ->method('delete')
            ->with($salesruleMock);

        $this->salesruleRegistryMock->expects($this->once())
            ->method('remove')
            ->with($salesruleId);

        $this->assertTrue($this->model->delete($salesruleMock));
    }

    /**
     * Test deleteById method
     */
    public function testDeleteById()
    {
        $salesruleId = 1;

        $this->salesruleRegistryMock->expects($this->once())
            ->method('retrieve')
            ->with($salesruleId)
            ->willReturn(null);

        $salesruleMock = $this->getMockForAbstractClass(SalesruleInterface::class);
        $salesruleMock->expects($this->atLeastOnce())
            ->method('getId')
            ->willReturn($salesruleId);
        $this->salesruleInterfaceFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($salesruleMock);

        $this->entityManagerMock->expects($this->once())
            ->method('load')
            ->with($salesruleMock, $salesruleId);
        $this->entityManagerMock->expects($this->once())
            ->method('delete')
            ->with($salesruleMock);

        $this->salesruleRegistryMock->expects($this->once())
            ->method('remove')
            ->with($salesruleId);

        $this->assertTrue($this->model->deleteById($salesruleId));
    }

    /**
     * Test deleteByRuleId method
     */
    public function testDeleteByRuleId()
    {
        $salesruleId = 1;
        $ruleId = 2;
        $ruleData = [
            'id' => $salesruleId
        ];

        $this->salesruleRegistryMock->expects($this->once())
            ->method('retrieveByRuleId')
            ->with($ruleId)
            ->willReturn(null);

        $salesruleResourceMock = $this->getMockBuilder(SalesruleResource::class)
            ->setMethods(['getByRuleId'])
            ->disableOriginalConstructor()
            ->getMock();
        $salesruleResourceMock->expects($this->once())
            ->method('getByRuleId')
            ->with($ruleId)
            ->willReturn($ruleData);
        $this->salesruleResourceFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($salesruleResourceMock);

        $salesruleMock = $this->getMockForAbstractClass(SalesruleInterface::class);
        $salesruleMock->expects($this->once())
            ->method('getId')
            ->willReturn($salesruleId);
        $this->salesruleInterfaceFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($salesruleMock);

        $this->dataObjectHelperMock->expects($this->once())
            ->method('populateWithArray')
            ->with($salesruleMock, $ruleData, SalesruleInterface::class);

        $this->entityManagerMock->expects($this->once())
            ->method('delete')
            ->with($salesruleMock);

        $this->salesruleRegistryMock->expects($this->once())
            ->method('removeByRuleId')
            ->with($ruleId);

        $this->assertTrue($this->model->deleteByRuleId($ruleId));
    }

    /**
     * Test save method
     */
    public function testSave()
    {
        $salesruleMock = $this->getMockForAbstractClass(SalesruleInterface::class);

        $this->salesruleRegistryMock->expects($this->once())
            ->method('push')
            ->with($salesruleMock);

        $this->entityManagerMock->expects($this->once())
            ->method('save')
            ->with($salesruleMock)
            ->willReturn($salesruleMock);

        $this->assertSame($salesruleMock, $this->model->save($salesruleMock));
    }
}
