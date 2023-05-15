<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Faq\Model\Email\Processors;

/**
 * Class QuestionFormProcessor
 * @package Aheadworks\Faq\Model\Email\Processors
 */
class QuestionFormProcessor implements ProcessorInterface
{
    /**
     * {@inheritdoc}
     */
    public function prepareVariables($variables)
    {
        $prepared = [];
        foreach ($variables as $key => $value) {
            $prepared[$key] = trim($value);
        }

        return $prepared;
    }
}
