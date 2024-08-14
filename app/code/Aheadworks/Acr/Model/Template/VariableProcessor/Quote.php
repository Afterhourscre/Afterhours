<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Acr\Model\Template\VariableProcessor;

use Magento\Quote\Model\ResourceModel\Quote\CollectionFactory as QuoteCollectionFactory;
use Aheadworks\Acr\Model\Source\Email\Variables;

/**
 * Class Quote
 *
 * @package Aheadworks\Acr\Model\Template\VariableProcessor
 */
class Quote implements VariableProcessorInterface
{
    /**
     * @var QuoteCollectionFactory
     */
    private $quoteCollectionFactory;

    public function __construct(
        QuoteCollectionFactory $quoteCollectionFactory
    ) {
        $this->quoteCollectionFactory = $quoteCollectionFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function process($quote, $params)
    {
        return [Variables::QUOTE => $quote];
    }

    /**
     * {@inheritdoc}
     */
    public function processTest($params)
    {
        return [Variables::QUOTE => $this->quoteCollectionFactory->create()
            ->addFilter('is_active', 1)
            ->getFirstItem()];
    }
}
