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
 * @package   mirasvit/module-seo-filter
 * @version   1.0.16
 * @copyright Copyright (C) 2020 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\SeoFilter\Plugin\Frontend\Catalog\Block\Product\ProductList\Toolbar;

use Mirasvit\SeoFilter\Model\Config;
use Mirasvit\SeoFilter\Service\UrlService;

class ToolbarUrlPlugin
{
    /**
     * @var UrlService
     */
    private $urlService;

    /**
     * @var Config
     */
    private $config;

    /**
     * ToolbarUrlPlugin constructor.
     * @param UrlService $urlHelper
     * @param Config $config
     */
    public function __construct(
        UrlService $urlHelper,
        Config $config
    ) {
        $this->urlService = $urlHelper;
        $this->config     = $config;
    }

    /**
     * @param object $subject
     * @param string $result
     *
     * @return string
     */
    public function afterGetPagerUrl($subject, $result)
    {
        if ($this->config->isApplicable()) {
            return $this->urlService->getQueryParams($result);
        }

        return $result;
    }
}
