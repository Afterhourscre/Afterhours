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
use Magento\Framework\App\ResourceConnection;
use Mirasvit\Review\Api\Data\ReviewInterface;


class StatusSource implements OptionSourceInterface
{
    private $resource;

    public function __construct(ResourceConnection $resource)
    {
        $this->resource = $resource;
    }

    public function toOptionArray()
    {
        $options = [];

        $statuses = $this->resource->getConnection()
            ->select()
            ->from($this->resource->getTableName(ReviewInterface::STATUS_TABLE))
            ->query();

        foreach ($statuses as $status) {
            $options[] = [
                'value' => $status[ReviewInterface::STATUS_ID],
                'label' => (string)__($status['status_code'])
            ];
        }

        return $options;
    }
}
