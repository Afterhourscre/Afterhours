<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Acr\Test\Unit\Model\Email\Template;

use Magento\Email\Model\TemplateFactory;
use Magento\Framework\App\Area;
use Magento\Store\Model\App\Emulation as AppEmulation;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\Acr\Model\Email\Template\Content;
use Magento\Email\Model\Template;

/**
 * Class ContentTest
 * @package Aheadworks\Acr\Test\Unit\Model\Email\Template
 */
class ContentTest extends TestCase
{
    /**
     * @var TemplateFactory
     */
    private $templateFactoryMock;

    /**
     * @var AppEmulation
     */
    private $appEmulationMock;

    /**
     * @var Content
     */
    private $content;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->templateFactoryMock = $this->createMock(TemplateFactory::class);
        $this->appEmulationMock = $this->createMock(AppEmulation::class);
        $this->content = $objectManager->getObject(
            Content::class,
            [
                'templateFactory' => $this->templateFactoryMock,
                'appEmulation' => $this->appEmulationMock
            ]
        );
    }

    /**
     * Test getTemplateContent method
     *
     * @param string $expected
     * @dataProvider getFullContentDataProvider
     */
    public function testGetTemplateContent($templateId)
    {
        $storeId = '1';
        $content = 'content';
        $template = $this->createMock(Template::class);

        $this->appEmulationMock->expects($this->once())
            ->method('startEnvironmentEmulation')
            ->with($storeId, Area::AREA_FRONTEND, true);
        $this->templateFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($template);
        if (is_numeric($templateId)) {
            $template->expects($this->once())
                ->method('load')
                ->with($templateId)
                ->willReturnSelf();
        } else {
            $template->expects($this->once())
                ->method('loadDefault')
                ->with($templateId)
                ->willReturnSelf();
        }
        $template->expects($this->once())
            ->method('__call')
            ->with('getTemplateText')
            ->willReturn($content);
        $this->appEmulationMock->expects($this->once())
            ->method('stopEnvironmentEmulation')
            ->willReturnSelf();

        $this->assertSame($content, $this->content->getTemplateContent($templateId, $storeId));
    }

    /**
     * Data provider for getFullContent method
     *
     * @return array
     */
    public function getFullContentDataProvider()
    {
        return [
            ['design_email_header_template'],
            ['1']
        ];
    }
}
