<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Acr\Model\Template;

use Magento\Framework\Filter\Template as TemplateFilter;

/**
 * Class PreviewFilter
 *
 * @package Magento\Framework\Filter\Template
 */
class PreviewFilter extends TemplateFilter
{
    /**
     * Filter the string as template.
     *
     * It is used to invoke page builder filter plugin
     *
     * @param string $value
     * @return string
     */
    public function filter($value)
    {
        return $value;
    }
}
