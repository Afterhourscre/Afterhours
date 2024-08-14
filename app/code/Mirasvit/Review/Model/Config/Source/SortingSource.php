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
 * @package   mirasvit/module-review
 * @version   1.1.2
 * @copyright Copyright (C) 2024 Mirasvit (https://mirasvit.com/)
 */


declare(strict_types=1);

namespace Mirasvit\Review\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class SortingSource implements OptionSourceInterface
{

    public function toOptionArray(): array
    {
        return [
            [
                'label'   => (string)__('Latest reviews'),
                'value'   => 'created_at-desc',
            ],
            [
                'label'   => (string)__('Oldest reviews'),
                'value'   => 'created_at-asc',
            ],
            [
                'label'   => (string)__('Rating high to low'),
                'value'   => 'total_rating-desc',
            ],
            [
                'label'   => (string)__('Rating low to high'),
                'value'   => 'total_rating-asc',
            ],
        ];
    }

}
