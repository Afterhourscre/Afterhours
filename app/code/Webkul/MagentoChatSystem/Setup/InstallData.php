<?php
/**
 * Webkul MagentoChatSystem Data Setup
 * @category  Webkul
 * @package   Webkul_MagentoChatSystem
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\MagentoChatSystem\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Authorization\Model\Acl\Role\Group as RoleGroup;
use Magento\Authorization\Model\UserContextInterface;

/**
 * @codeCoverageIgnore
 */
class InstallData implements InstallDataInterface
{
    /**
     * RoleFactory
     *
     * @var roleFactory
     */
    private $roleFactory;

     /**
      * RulesFactory
      *
      * @var rulesFactory
      */
    private $rulesFactory;
    /**
     * Init
     *
     * @param \Magento\Authorization\Model\RoleFactory $roleFactory
     * @param \Magento\Authorization\Model\RulesFactory $rulesFactory
     */
    public function __construct(
        \Magento\Authorization\Model\RoleFactory $roleFactory,
        \Magento\Authorization\Model\RulesFactory $rulesFactory
    ) {
        $this->roleFactory = $roleFactory;
        $this->rulesFactory = $rulesFactory;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
       /**
        * Create Chatsystem role
        */
        $role = $this->roleFactory->create();
        $collection = $role->getCollection()
            ->addFieldToFilter('role_name', 'ChatSystem');
        if (!$collection->getSize()) {
            $role->setName('ChatSystem')
                ->setPid(0)
                ->setRoleType(RoleGroup::ROLE_TYPE)
                ->setUserType(UserContextInterface::USER_TYPE_ADMIN);

            $role->save();
            $resource = [
                'Magento_Backend::admin',
                'Webkul_MagentoChatSystem::chatsystem',
                'Webkul_MagentoChatSystem::menu',
                'Webkul_MagentoChatSystem::assigned_view',
                'Webkul_MagentoChatSystem::agents',
                'Webkul_MagentoChatSystem::AgentRating_view'
            ];
            $this->rulesFactory->create()->setRoleId($role->getId())->setResources($resource)->saveRel();
        }
        

        $role = $this->roleFactory->create();
        $collection = $role->getCollection()
            ->addFieldToFilter('role_name', 'ChatManager');
        if (!$collection->getSize()) {
                $role->setName('ChatManager')
                ->setPid(0)
                ->setRoleType(RoleGroup::ROLE_TYPE)
                ->setUserType(UserContextInterface::USER_TYPE_ADMIN);
                
            $role->save();
            $resource = [
                'Magento_Backend::admin',
                'Webkul_MagentoChatSystem::chatsystem',
                'Webkul_MagentoChatSystem::menu',
                'Webkul_MagentoChatSystem::assigned',
                'Webkul_MagentoChatSystem::assigned_update',
                'Webkul_MagentoChatSystem::agents',
                'Webkul_MagentoChatSystem::feedback',
                'Webkul_MagentoChatSystem::AgentRating_update',
                'Webkul_MagentoChatSystem::AgentRating_save',
                'Webkul_MagentoChatSystem::AgentRating_view',
                'Magento_Backend::stores',
                'Magento_Backend::stores_settings',
                'Magento_Config::config',
                'Webkul_MagentoChatSystem::config_chatsystem'
            ];
            $this->rulesFactory->create()->setRoleId($role->getId())->setResources($resource)->saveRel();
        }
    }
}
