<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Acr\Test\Unit\Model\Preview;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;
use Magento\Framework\Session\Storage as SessionStorage;
use Magento\Framework\Registry;
use Aheadworks\Acr\Model\Preview\Storage;

/**
 * Class StorageTest
 * @package Aheadworks\Acr\Test\Unit\Model\Preview
 */
class StorageTest extends TestCase
{
    /**
     * @var SessionStorage
     */
    private $sessionStorageMock;

    /**
     * @var Storage
     */
    private $model;

    /**
     * @var Registry
     */
    private $coreRegistryMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->sessionStorageMock = $this->createMock(SessionStorage::class);
        $this->coreRegistryMock = $this->createMock(Registry::class);

        $this->model = $objectManager->getObject(
            Storage::class,
            [
                'sessionStorage' => $this->sessionStorageMock,
                'coreRegistry' => $this->coreRegistryMock
            ]
        );
    }

    /**
     * Test getEmailData method
     */
    public function testGetEmailData()
    {
        $data = ['test', 'test', 'test'];
        $this->sessionStorageMock->expects($this->once())
            ->method('getData')
            ->with(Storage::SESSION_NAME)
            ->willReturn($data);

        $this->assertEquals($data, $this->model->getEmailData());
    }

    /**
     * Test saveEmailData method
     */
    public function testSaveEmailData()
    {
        $data = ['test', 'test', 'test'];
        $this->sessionStorageMock->expects($this->once())
            ->method('setData')
            ->with(Storage::SESSION_NAME, $data);

        $this->model->saveEmailData($data);
    }

    /**
     * Test getPreviewData method
     */
    public function testGetPreviewData()
    {
        $data = ['test', 'test', 'test'];
        $this->coreRegistryMock->expects($this->once())
            ->method('registry')
            ->with(Storage::REGISTRY_NAME)
            ->willReturn($data);

        $this->assertEquals($data, $this->model->getPreviewData());
    }

    /**
     * Test savePreviewData method
     * @dataProvider exist
     */
    public function testSavePreviewData($exist)
    {
        $data = ['test', 'test', 'test'];
        $this->sessionStorageMock = $this->createMock(SessionStorage::class);
        if ($exist) {
            $this->coreRegistryMock->expects($this->at(0))
                ->method('register')
                ->with(Storage::REGISTRY_NAME, $data)
                ->willThrowException(new \RuntimeException());

            $this->coreRegistryMock->expects($this->at(1))
                ->method('unregister')
                ->with(Storage::REGISTRY_NAME);
            $this->coreRegistryMock->expects($this->at(2))
                ->method('register')
                ->with(Storage::REGISTRY_NAME, $data);

            $this->model->savePreviewData($data);
        } else {
            $this->coreRegistryMock->expects($this->once())
                ->method('register')
                ->with(Storage::REGISTRY_NAME, $data);

            $this->model->savePreviewData($data);
        }
    }

    /**
     * @return array
     */
    public function exist()
    {
        return [
            [true], [false]
        ];
    }
}
