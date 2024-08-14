<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace MageCloud\MGSPortfolioSort\Block;

use \Magento\Framework\ObjectManagerInterface;
use \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Category
{
    /**
     * Category constructor.
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(
        ObjectManagerInterface $objectManager
    )
    {
        $this->_objectManager = $objectManager;
    }

    public function afterGetPortfolios(\MGS\Portfolio\Block\Category $subject, $result){


        if ($result instanceof AbstractCollection) {
            $result->setOrder('sort_order', 'asc');
        }

        return $result;
    }
}
