<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-seo
 * @version   2.0.169
 * @copyright Copyright (C) 2020 Mirasvit (https://mirasvit.com/)
 */


namespace Mirasvit\SeoSitemap\Service;

use Mirasvit\SeoSitemap\Api\Service\SeoSitemapInitItemsServiceInterface;
use Magento\Framework\Module\Manager as ModuleManager;

class SeoSitemapInitItemsService
{
    /**
     * @var SeoSitemapInitItemsServiceInterface[]
     */
    private $itemHandlers;

    /**
     * @var array
     */
    private $whitelist = ['Product', 'Category', 'Cms'];

    public function __construct(
        array $itemHandlers = [],
        ModuleManager $moduleManager
    ) {
        $this->itemHandlers = $itemHandlers;
        $this->moduleManager        = $moduleManager;
    }

    /**
     * {@inheritdoc}
     */
    public function initSitemapItems($storeId)
    {
        foreach ($this->itemHandlers as $itemKey => $itemHandler) {
            if ($this->canUseInSiteMap($itemKey)) {
                    $result = $itemHandler->initSitemapItem($storeId);
                if (!empty($result)) {
                    yield $result;
                }
            }
        }
    }


}
