<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Coupongenerator\Model\Source\Rule;

use Magento\Framework\Data\OptionSourceInterface;
use Aheadworks\Coupongenerator\Model\ResourceModel\Salesrule\CollectionFactory as SalesruleCollectionFactory;

/**
 * Class Name
 * @package Aheadworks\Coupongenerator\Model\Source\Rule
 */
class Name implements OptionSourceInterface
{
    /**
     * @var SalesruleCollectionFactory
     */
    private $salesruleCollectionFactory;

    /**
     * @param SalesruleCollectionFactory $salesruleCollectionFactory
     */
    public function __construct(
        SalesruleCollectionFactory $salesruleCollectionFactory
    ) {
        $this->salesruleCollectionFactory = $salesruleCollectionFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        $options = $this->salesruleCollectionFactory->create()
            ->setActiveRules()
            ->toOptionArray()
        ;
        return $options;
    }
}
