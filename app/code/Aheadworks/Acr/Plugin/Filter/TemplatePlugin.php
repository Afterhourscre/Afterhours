<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Acr\Plugin\Filter;

use Aheadworks\Acr\Model\ThirdPartyModule\Manager;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Filter\Template;

/**
 * Class TemplatePlugin
 *
 * @package Aheadworks\Acr\Plugin\Filter
 */
class TemplatePlugin
{
    /**
     * @var Manager
     */
    private $moduleManager;

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @param Manager $moduleManager
     * @param ObjectManagerInterface $objectManeger
     */
    public function __construct(
        Manager $moduleManager,
        ObjectManagerInterface $objectManeger
    ) {
        $this->moduleManager = $moduleManager;
        $this->objectManager = $objectManeger;
    }

    /**
     * Execute page builder plugin after filter method
     *
     * @param Template $subject
     * @param string $result
     * @return string
     */
    public function afterFilter($subject, $result)
    {
        if ($this->moduleManager->isMagentoPageBuilderModuleEnabled()) {
            /** @var \Magento\PageBuilder\Plugin\Filter\TemplatePlugin $pageBuilderPlugin */
            $pageBuilderPlugin = $this->objectManager->get(
                \Magento\PageBuilder\Plugin\Filter\TemplatePlugin::class
            );
            $result = $pageBuilderPlugin->afterFilter($subject, $result);
        }

        return $result;
    }

    /**
     * Execute page builder plugin around customvar directive method
     *
     * @param Template $subject
     * @param \Closure $proceed
     * @param string[] $construction
     * @return string
     */
    public function aroundCustomvarDirective($subject, $proceed, $construction)
    {
        if ($this->moduleManager->isMagentoPageBuilderModuleEnabled()) {
            /** @var \Magento\PageBuilder\Plugin\Filter\TemplatePlugin $pageBuilderPlugin */
            $pageBuilderPlugin = $this->objectManager->get(
                \Magento\PageBuilder\Plugin\Filter\TemplatePlugin::class
            );
            $result = $pageBuilderPlugin->aroundCustomvarDirective($subject, $proceed, $construction);
        } else {
            $result = $proceed($construction);
        }

        return $result;
    }
}
