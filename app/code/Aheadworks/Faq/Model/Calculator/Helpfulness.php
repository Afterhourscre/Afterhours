<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Faq\Model\Calculator;

use Aheadworks\Faq\Api\Data\ArticleInterface;

/**
 * Class Calculator
 * @package Aheadworks\Faq\Model\Layout\Processor
 */
class Helpfulness
{
    /**
     * Calculate helpfulness rating
     *
     * @param ArticleInterface $article
     * @return float
     */
    public function calculateHelpfulnessRating($article)
    {
        $votesYes = $article->getVotesYes();
        $totalVotes = $article->getVotesNo() + $votesYes;
        $helpfulPercent = 0;

        if ($totalVotes) {
            $helpfulPercent = ceil($votesYes / ($totalVotes) * 100);
        }

        return $helpfulPercent;
    }
}
