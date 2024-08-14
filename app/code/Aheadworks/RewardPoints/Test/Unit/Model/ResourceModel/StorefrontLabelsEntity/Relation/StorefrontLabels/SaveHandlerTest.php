<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\RewardPoints\Test\Unit\Model\ResourceModel\StorefrontLabelsEntity\Relation\StorefrontLabels;

use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\RewardPoints\Model\ResourceModel\StorefrontLabelsEntity\Relation\StorefrontLabels\SaveHandler;
use Aheadworks\RewardPoints\Api\Data\StorefrontLabelsEntityInterface;
use Aheadworks\RewardPoints\Model\ResourceModel\StorefrontLabels\Repository;

/**
 * Test for \Aheadworks\RewardPoints\Model\ResourceModel\StorefrontLabelsEntity\Relation\StorefrontLabels\SaveHandler
 */
class SaveHandlerTest extends TestCase
{
    /**
     * @var SaveHandler
     */
    private $saveHandler;

    /**
     * @var Repository|\PHPUnit_Framework_MockObject_MockObject
     */
    private $repositoryMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->repositoryMock = $this->createMock(
            Repository::class
        );

        $this->saveHandler = $objectManager->getObject(
            SaveHandler::class,
            [
                'repository' => $this->repositoryMock,
            ]
        );
    }

    /**
     * Test for execute() method
     *
     * @param bool $resultOfSaving
     * @dataProvider executeSuccessfulDataProvider
     */
    public function testExecuteSuccessful($resultOfSaving)
    {
        $arguments = [];

        $entity = $this->createMock(StorefrontLabelsEntityInterface::class);

        $this->repositoryMock->expects($this->once())
            ->method('save')
            ->with($entity)
            ->willReturn($resultOfSaving);

        $this->assertSame($entity, $this->saveHandler->execute($entity, $arguments));
    }

    /**
     * @return array
     */
    public function executeSuccessfulDataProvider()
    {
        return [
            [
                'resultOfSaving' => false,
            ],
            [
                'resultOfSaving' => true,
            ],
        ];
    }

    /**
     * Test for execute() method
     *
     * @expectedException \Exception
     * @expectedExceptionMessage Error!
     */
    public function testExecuteException()
    {
        $arguments = [];

        $entity = $this->createMock(StorefrontLabelsEntityInterface::class);

        $this->repositoryMock->expects($this->once())
            ->method('save')
            ->with($entity)
            ->willThrowException(new \Exception("Error!"));

        $this->saveHandler->execute($entity, $arguments);
    }
}
