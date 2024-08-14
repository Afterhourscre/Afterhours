<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Faq\Model\Email\Processors;

/**
 * Interface ProcessorInterface
 * @package Aheadworks\Faq\Model\Email\Processors
 */
interface ProcessorInterface
{
    /**
     * Prepare variables before send
     *
     * @param array $variables
     * @return array
     */
    public function prepareVariables($variables);
}
