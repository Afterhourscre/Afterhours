<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Model\Rule;

use Aheadworks\OnSale\Model\Indexer\Rule\Processor as RuleProcessor;
use Magento\Framework\DataObject;

/**
 * Class Job
 *
 * @method setSuccess(string $errorMessage)
 * @method setError(string $errorMessage)
 * @method string getSuccess()
 * @method string getError()
 * @method bool hasSuccess()
 * @method bool hasError()
 * @package Aheadworks\OnSale\Model\Rule
 */
class Job extends DataObject
{
    /**
     * @var RuleProcessor
     */
    protected $ruleProcessor;

    /**
     * Basic object initialization
     *
     * @param RuleProcessor $ruleProcessor
     * @param array $data
     */
    public function __construct(
        RuleProcessor $ruleProcessor,
        array $data = []
    ) {
        parent::__construct($data);
        $this->ruleProcessor = $ruleProcessor;
    }

    /**
     * Mark rule indexer as invalid
     *
     * @return $this
     */
    public function applyAll()
    {
        try {
            $this->ruleProcessor->markIndexerAsInvalid();
            $this->setSuccess(__('Rules have been applied.'));
        } catch (\Exception $e) {
            $this->setError($e->getMessage());
        }
        return $this;
    }
}
