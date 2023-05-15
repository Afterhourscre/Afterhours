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



namespace Mirasvit\SeoFilter\Plugin\Frontend\Framework\App\Router\Base;

use Magento\Framework\App\RequestInterface;
use Mirasvit\SeoFilter\Model\Config;
use Mirasvit\SeoFilter\Service\ParserService;

class RouterPlugin
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var ParserService
     */
    private $parserService;

    /**
     * RouterPlugin constructor.
     * @param ParserService $parserService
     * @param Config $config
     */
    public function __construct(
        ParserService $parserService,
        Config $config
    ) {
        $this->parserService = $parserService;
        $this->config        = $config;
    }

    /**
     * Apply friendly filters
     *
     * @param object           $subject
     * @param RequestInterface $request
     *
     * @return void
     */
    public function beforeMatch($subject, RequestInterface $request)
    {
        if ($this->config->isEnabled()) {
            $params = $this->parserService->getParams();

            if ($params) {
                /** @var \Magento\Framework\App\Request\Http $request */
                $request->setRouteName('catalog')
                    ->setModuleName('catalog')
                    ->setControllerName('category')
                    ->setActionName('view')
                    ->setParam('id', $params['category_id'])
                    ->setParams($params['params']);
            }
        }
    }
}
