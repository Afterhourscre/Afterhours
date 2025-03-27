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
 * @version   2.9.6
 * @copyright Copyright (C) 2024 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Seo\Model\Config\Source\AssociatedCanonical;

use Magento\Framework\Option\ArrayInterface;
use Mirasvit\Seo\Model\Config;

class CategoryFiltered implements ArrayInterface
{
    public function toOptionArray(): array
    {
        return [
            ['value' => Config::CANONICAL_LAYERED_NOFILTERS, 'label' => __('Category URL without filters')],
            ['value' => Config::CANONICAL_LAYERED_ALL, 'label' => __('Category URL with all applied filters')],
            ['value' => Config::CANONICAL_LAYERED_CONFIG, 'label' => __('Custom')],
        ];
    }
}
