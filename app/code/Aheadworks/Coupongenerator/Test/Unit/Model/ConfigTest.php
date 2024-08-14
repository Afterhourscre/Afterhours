<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Coupongenerator\Test\Unit\Model;

use Aheadworks\Coupongenerator\Model\Config;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Test for \Aheadworks\Coupongenerator\Model\Config
 */
class ConfigTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Config
     */
    private $configModel;

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
        $this->configModel = $objectManager->getObject(
            Config::class,
            [
                'scopeConfig' => $this->scopeConfigMock
            ]
        );
    }

    /**
     * Test getEmailSender method
     */
    public function testGetEmailSender()
    {
        $websiteId = 1;
        $emailSender = 'general';
        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(Config::XML_PATH_SENDER_IDENTITY, ScopeInterface::SCOPE_WEBSITE, $websiteId)
            ->willReturn($emailSender);
        $this->assertEquals($emailSender, $this->configModel->getEmailSender($websiteId));
    }

    /**
     * Test getEmailSenderName method
     */
    public function testGetEmailSenderName()
    {
        $websiteId = 1;
        $emailSender = 'general';
        $emailSenderName = 'General Contact';

        $this->scopeConfigMock->expects($this->at(0))
            ->method('getValue')
            ->with(Config::XML_PATH_SENDER_IDENTITY, ScopeInterface::SCOPE_WEBSITE, $websiteId)
            ->willReturn($emailSender);

        $this->scopeConfigMock->expects($this->at(1))
            ->method('getValue')
            ->with('trans_email/ident_' . $emailSender . '/name', ScopeInterface::SCOPE_WEBSITE, $websiteId)
            ->willReturn($emailSenderName);

        $this->assertEquals($emailSenderName, $this->configModel->getEmailSenderName($websiteId));
    }

    /**
     * Test getEmailTemplate method
     */
    public function testGetEmailTemplate()
    {
        $storeId = 1;
        $emailTemplate = 'aw_coupongenerator_general_email_template';
        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(Config::XML_PATH_TEMPLATE_IDENTITY, ScopeInterface::SCOPE_STORE, $storeId)
            ->willReturn($emailTemplate);
        $this->assertEquals($emailTemplate, $this->configModel->getEmailTemplate($storeId));
    }
}
