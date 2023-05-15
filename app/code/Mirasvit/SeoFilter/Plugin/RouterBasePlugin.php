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
 * @version   1.0.12
 * @copyright Copyright (C) 2019 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\SeoFilter\Plugin;

use Magento\Framework\App\RequestInterface;
use Magento\Store\Model\StoreManagerInterface;
use Mirasvit\SeoFilter\Api\Config\ConfigInterface as Config;
use Mirasvit\SeoFilter\Api\Data\RewriteInterface;
use Mirasvit\SeoFilter\Api\Service\ParserServiceInterface;

class RouterBasePlugin
{
    public function __construct(
        ParserServiceInterface $parser,
        StoreManagerInterface $storeManager,
        Config $config
    ) {
        $this->parser       = $parser;
        $this->storeManager = $storeManager;
        $this->config       = $config;
    }

    /**
     * Modify category
     *
     * @param \Magento\Framework\App\Router\Base $subject
     * @param RequestInterface                   $request
     *
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeMatch($subject, RequestInterface $request)
    {
        if ($this->config->isEnabled($this->storeManager->getStore()->getId())) {
            $parsedResult = $this->parser->parseFilterInformationFromRequest();

            if ($parsedResult) {
                $request->setRouteName('catalog')
                    ->setModuleName('catalog')
                    ->setControllerName('category')
                    ->setActionName('view')
                    ->setParam('id', $parsedResult[RewriteInterface::CATEGORY_ID])
                    ->setParams($parsedResult[RewriteInterface::PARAMS]);
            }
        }
    }
}