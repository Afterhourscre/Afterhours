<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Acr\Test\Unit\Model\Email;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;
use Aheadworks\Acr\Model\Email\UrlBuilder;
use Aheadworks\Acr\Model\Email\UrlInterface as UrlInterfaceFront;
use Magento\Backend\Model\UrlInterface as UrlInterfaceBack;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class UrlBuilderTest
 * @package Aheadworks\Acr\Test\Unit\Model\Email
 */
class UrlBuilderTest extends TestCase
{
    /**
     * @var UrlBuilder
     */
    private $url;

    /**
     * @var UrlInterface
     */
    private $front;

    /**
     * @var UrlInterface
     */
    private $back;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->front = $this->createMock(UrlInterfaceFront::class);
        $this->back = $this->createMock(UrlInterfaceBack::class);

        $this->url = $objectManager->getObject(
            UrlBuilder::class,
            [
                'urlBuilders' => [
                    'frontend' => $this->front,
                    'adminhtml' => $this->back
                ]
            ]
        );
    }

    /**
     * Test Process method
     * @dataProvider data
     */
    public function testGetUrl($bool, $areaCode)
    {
        $href = $bool ? 'http://123.com/path/to/model' : null;
        $path = 'path/to/model';
        $params = [];
        if ($areaCode == 'frontend') {
            $urlBuilder = $this->front;
        } elseif ($areaCode == 'adminhtml') {
            $urlBuilder = $this->back;
        }
        $scope = $this->createMock(StoreManagerInterface::class);
        if ($bool) {
            $urlBuilder->expects($this->once())
                ->method('setScope')
                ->willReturnSelf();
            $urlBuilder->expects($this->once())
                ->method('getUrl')
                ->with($path, $params)
                ->willReturn($href);
        }

        $this->assertSame($href, $this->url->getUrl($path, $scope, $params, $areaCode));
    }

    /**
     * @return array
     */
    public function data()
    {
        return [
            [true, 'frontend'],
            [false, 'frontend'],
            [true, 'adminhtml'],
            [false, 'adminhtml']
        ];
    }
}
