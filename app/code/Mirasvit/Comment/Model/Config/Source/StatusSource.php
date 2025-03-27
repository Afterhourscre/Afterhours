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
 * @package   mirasvit/module-comment
 * @version   1.0.1
 * @copyright Copyright (C) 2024 Mirasvit (https://mirasvit.com/)
 */


declare(strict_types=1);


namespace Mirasvit\Comment\Model\Config\Source;


use Magento\Framework\Data\OptionSourceInterface;
use Mirasvit\Comment\Api\Data\CommentInterface;

class StatusSource implements OptionSourceInterface
{
    public function toOptionArray()
    {
        return [
            [
                'label' => (string)__('Pending'),
                'value' => CommentInterface::STATUS_PENDING,
            ],
            [
                'label' => (string)__('Approved'),
                'value' => CommentInterface::STATUS_APPROVED,
            ],
            [
                'label' => (string)__('Rejected'),
                'value' => CommentInterface::STATUS_REJECTED,
            ],
        ];
    }
}
