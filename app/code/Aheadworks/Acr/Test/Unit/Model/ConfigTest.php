<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Acr\Test\Unit\Model;

use Aheadworks\Acr\Model\Config;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Store\Model\ScopeInterface;
use PHPUnit\Framework\TestCase;

/**
 * Class ConfigTest
 * Test for \Aheadworks\Acr\Model\Config
 *
 * @package Aheadworks\Acr\Test\Unit\Model
 */
class ConfigTest extends TestCase
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var ScopeConfigInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $scopeConfigMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->scopeConfigMock = $this->getMockForAbstractClass(ScopeConfigInterface::class);
        $this->config = $objectManager->getObject(
            Config::class,
            ['scopeConfig' => $this->scopeConfigMock]
        );
    }

    /**
     * Test getSender method
     */
    public function testGetSender()
    {
        $sender = 'general';

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(Config::XML_PATH_SENDER, ScopeInterface::SCOPE_STORE)
            ->willReturn($sender);
        $this->assertEquals(
            $sender,
            $this->config->getSender()
        );
    }

    /**
     * Test getTestEmailRecipient method
     */
    public function testGetTestEmailRecipient()
    {
        $testEmail = 'test@example.com';

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(Config::XML_PATH_TEST_EMAIL_RECIPIENT, ScopeInterface::SCOPE_STORE)
            ->willReturn($testEmail);
        $this->assertEquals(
            $testEmail,
            $this->config->getTestEmailRecipient()
        );
    }

    /**
     * Test isTestModeEnabled method
     *
     * @param bool $value
     * @dataProvider boolDataProvider
     */
    public function testIsTestModeEnabled($value)
    {
        $this->scopeConfigMock->expects($this->once())
            ->method('isSetFlag')
            ->with(Config::XML_PATH_ENABLE_TEST_MODE, ScopeInterface::SCOPE_STORE)
            ->willReturn($value);
        $this->assertSame($value, $this->config->isTestModeEnabled());
    }

    /**
     * Test getTestKeepEmailsFor method
     */
    public function testGetTestKeepEmailsFor()
    {
        $keepEmailsFor = 60;

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(Config::XML_PATH_MAIL_LOG_KEEP_FOR)
            ->willReturn($keepEmailsFor);
        $this->assertEquals(
            $keepEmailsFor,
            $this->config->getKeepEmailsFor()
        );
    }

    /**
     * Test getHeaderTemplateId method
     */
    public function testGetHeaderTemplateId()
    {
        $templateId = '1';

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(Config::XML_PATH_HEADER_TEMPLATE, ScopeInterface::SCOPE_STORE)
            ->willReturn($templateId);
        $this->assertEquals(
            $templateId,
            $this->config->getHeaderTemplateId()
        );
    }

    /**
     * Test getFooterTemplateId method
     */
    public function testGetFooterTemplateId()
    {
        $templateId = '1';

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(Config::XML_PATH_FOOTER_TEMPLATE, ScopeInterface::SCOPE_STORE)
            ->willReturn($templateId);
        $this->assertEquals(
            $templateId,
            $this->config->getFooterTemplateId()
        );
    }

    /**
     * @return array
     */
    public function boolDataProvider()
    {
        return [[true], [false]];
    }
}
