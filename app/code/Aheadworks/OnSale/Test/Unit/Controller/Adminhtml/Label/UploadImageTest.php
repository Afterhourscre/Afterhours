<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Test\Unit\Controller\Adminhtml\Label;

use Aheadworks\OnSale\Controller\Adminhtml\Label\UploadImage;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\OnSale\Model\Label\Image\Uploader;
use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\Json;

/**
 * Class UploadImageTest
 *
 * @package Aheadworks\OnSale\Test\Unit\Controller\Adminhtml\Label
 */
class UploadImageTest extends TestCase
{
    /**
     * @var UploadImage
     */
    private $controller;

    /**
     * @var ResultFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $resultFactoryMock;

    /**
     * @var Uploader|\PHPUnit_Framework_MockObject_MockObject
     */
    private $imageUploaderMock;

    /**
     * @var Json|\PHPUnit_Framework_MockObject_MockObject
     */
    private $resultJsonMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->resultFactoryMock = $this->createPartialMock(ResultFactory::class, ['create']);
        $this->imageUploaderMock = $this->createPartialMock(Uploader::class, ['uploadToMediaFolder']);
        $context = $objectManager->getObject(
            Context::class,
            [
                'resultFactory' => $this->resultFactoryMock
            ]
        );

        $this->resultJsonMock = $this->createPartialMock(Json::class, ['setData']);
        $this->resultFactoryMock->expects($this->once())
            ->method('create')
            ->with(ResultFactory::TYPE_JSON)
            ->willReturn($this->resultJsonMock);

        $this->controller = $objectManager->getObject(
            UploadImage::class,
            [
                'context' => $context,
                'imageUploader' => $this->imageUploaderMock
            ]
        );
    }

    /**
     * Test execute method
     */
    public function testExecute()
    {
        $imageUploaderResult = ['file' => 'name.png', 'size' => '2000', 'type' => 'image', 'url' => 'url'];

        $this->imageUploaderMock->expects($this->once())
            ->method('uploadToMediaFolder')
            ->with(UploadImage::FILE_ID)
            ->willReturn($imageUploaderResult);

        $this->resultJsonMock->expects($this->once())
            ->method('setData')
            ->with($imageUploaderResult)
            ->willReturnSelf();

        $this->assertSame($this->resultJsonMock, $this->controller->execute());
    }

    /**
     * Test execute method on exception
     */
    public function testExecuteOnException()
    {
        $exceptionData = ['error' => 'Exception message.', 'errorcode' => 0];
        $exception = new \Exception($exceptionData['error']);

        $this->imageUploaderMock->expects($this->once())
            ->method('uploadToMediaFolder')
            ->with(UploadImage::FILE_ID)
            ->willThrowException($exception);

        $this->resultJsonMock->expects($this->once())
            ->method('setData')
            ->with($exceptionData)
            ->willReturnSelf();

        $this->assertSame($this->resultJsonMock, $this->controller->execute());
    }
}
