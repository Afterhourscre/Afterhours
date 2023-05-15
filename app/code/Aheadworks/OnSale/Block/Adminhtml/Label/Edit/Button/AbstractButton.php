<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Block\Adminhtml\Label\Edit\Button;

use Magento\Backend\Block\Widget\Context;

/**
 * Class AbstractButton
 *
 * @package Aheadworks\OnSale\Block\Adminhtml\Label\Edit\Button
 */
abstract class AbstractButton
{
    /**
     * @var Context
     */
    protected $context;

    /**
     * AbstractButton constructor.
     * @param Context $context
     */
    public function __construct(
        Context $context
    ) {
        $this->context = $context;
    }

    /**
     * Generate url by route and parameters
     *
     * @param string $route
     * @param array $params
     * @return string
     */
    protected function getUrl($route = '', $params = [])
    {
        return $this->context->getUrlBuilder()->getUrl($route, $params);
    }
}
