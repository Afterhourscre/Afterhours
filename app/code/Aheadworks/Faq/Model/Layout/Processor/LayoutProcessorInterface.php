<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Faq\Model\Layout\Processor;

use Aheadworks\Faq\Model\Article;

/**
 * Interface LayoutProcessorInterface
 * @package Aheadworks\Faq\Model\Helpfulness\Layout\Processor
 */
interface LayoutProcessorInterface
{
    /**
     * Process js Layout of block
     *
     * @param array $jsLayout
     * @param Article $article
     * @return array
     */
    public function process($jsLayout, $article);
}
