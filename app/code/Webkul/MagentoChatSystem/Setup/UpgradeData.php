<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_MagentoChatSystem
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\MagentoChatSystem\Setup;

use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;

/**
 * Upgrade Data script
 * @codeCoverageIgnore
 */
class UpgradeData implements UpgradeDataInterface
{
    /**
     * EAV setup factory
     *
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * @param EavSetupFactory       $eavSetupFactory
     */
    public function __construct(
        EavSetupFactory $eavSetupFactory
    ) {
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        
        /** @var \Magento\Framework\ObjectManagerInterface $objManager */
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        /** @var \Magento\Framework\Module\Dir\Reader $reader */
        $reader = $objectManager->get('Magento\Framework\Module\Dir\Reader');
        /** @var \Magento\Framework\Filesystem $filesystem */
        $filesystem = $objectManager->get('Magento\Framework\Filesystem\Io\File');
        $_filesystem = $objectManager->get('Magento\Framework\Filesystem');
        $directorySystem = $objectManager->get('\Magento\Framework\App\Filesystem\DirectoryList');
        $rootFullPath = $directorySystem->getRoot();
        if (!$filesystem->fileExists($rootFullPath.'/app.js')) {
            $serverJs = $reader->getModuleDir(
                '',
                'Webkul_MagentoChatSystem'
            ).'/etc/serverJs/app.js';
            $filesystem->cp($serverJs, $rootFullPath.'/app.js');
        }

        if (!$filesystem->fileExists($rootFullPath.'/package.json')) {
            $serverJs = $reader->getModuleDir(
                '',
                'Webkul_MagentoChatSystem'
            ).'/etc/serverJs/package.json';
            $filesystem->cp($serverJs, $rootFullPath.'/package.json');
        }

        $mediaFullPath = $_filesystem->getDirectoryRead(
            \Magento\Framework\App\Filesystem\DirectoryList::MEDIA
        )->getAbsolutePath('chatsystem');
        if (!$filesystem->fileExists($mediaFullPath)) {
            $filesystem->mkdir($mediaFullPath, 0777, true);
            $defaultImage = $reader->getModuleDir(
                '',
                'Webkul_MagentoChatSystem'
            ).'/view/frontend/web/images/default.png';
            $filesystem->cp($defaultImage, $mediaFullPath.'/default.png');
        }

        $mediaFullPath = $_filesystem->getDirectoryRead(
            \Magento\Framework\App\Filesystem\DirectoryList::MEDIA
        )->getAbsolutePath('chatsystem/admin');

        if (!$filesystem->fileExists($mediaFullPath)) {
            $filesystem->mkdir($mediaFullPath, 0777, true);
            $defaultImage = $reader->getModuleDir(
                '',
                'Webkul_MagentoChatSystem'
            ).'/view/adminhtml/web/images/default.png';
            $filesystem->cp($defaultImage, $mediaFullPath.'/default.png');
        }

        $setup->endSetup();
    }
}
