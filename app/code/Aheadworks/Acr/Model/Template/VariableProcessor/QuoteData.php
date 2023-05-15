<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Acr\Model\Template\VariableProcessor;

use Magento\Quote\Model\ResourceModel\Quote\CollectionFactory as QuoteCollectionFactory;
use Aheadworks\Acr\Model\Hydrator\Quote;

/**
 * Class QuoteData
 *
 * @package Aheadworks\Acr\Model\Template\VariableProcessor
 */
class QuoteData implements VariableProcessorInterface
{
    /**
     * @var QuoteCollectionFactory
     */
    private $quoteCollectionFactory;

    /**
     * @var Quote
     */
    private $hydrator;

    /**
     * @param QuoteCollectionFactory $quoteCollectionFactory
     */
    public function __construct(
        QuoteCollectionFactory $quoteCollectionFactory,
        Quote $hydrator
    ) {
        $this->quoteCollectionFactory = $quoteCollectionFactory;
        $this->hydrator = $hydrator;
    }

    /**
     * {@inheritdoc}
     */
    public function process($quote, $params)
    {
        return $this->hydrator->extract($quote);
    }

    /**
     * {@inheritdoc}
     */
    public function processTest($params)
    {
        $quote = $this->quoteCollectionFactory->create()
            ->addFilter('is_active', 1)
            ->getFirstItem();
        return $this->hydrator->extract($quote);
    }
}
