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

namespace Mirasvit\Review\Model\ResourceModel\ReviewSummary;

use Mirasvit\Review\Api\Data\ReviewSummaryInterface;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = ReviewSummaryInterface::ID;

    protected $_eventPrefix = 'mst_review_summary_collection';

    protected $_eventObject = 'summary_review_collection';

    protected function _construct()
    {
        $this->_init('Mirasvit\Review\Model\ReviewSummary', 'Mirasvit\Review\Model\ResourceModel\ReviewSummary');
    }

}
