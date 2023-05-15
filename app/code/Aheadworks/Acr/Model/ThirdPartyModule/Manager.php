<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Acr\Model\ThirdPartyModule;

use Magento\Framework\Module\ModuleListInterface;
use Magento\Framework\ObjectManagerInterface;

/**
 * Class Manager
 *
 * @package Aheadworks\Acr\Model\ThirdPartyModule
 */
class Manager
{
    /**
     * Magento page builder module name
     */
    const PAGE_BUILDER_MODULE_NAME = 'Magento_PageBuilder';

    /**
     * @var ModuleListInterface
     */
    private $moduleList;

    /**
     * @param ModuleListInterface $moduleList
     */
    public function __construct(
        ModuleListInterface $moduleList
    ) {
        $this->moduleList = $moduleList;
    }

    /**
     * Check if Magento page builder module enabled
     *
     * @return bool
     */
    public function isMagentoPageBuilderModuleEnabled()
    {
        return $this->moduleList->has(self::PAGE_BUILDER_MODULE_NAME);
    }
}
