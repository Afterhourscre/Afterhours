<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Faq\Model;

use Aheadworks\Faq\Api\HelpfulnessManagementInterface;
use Aheadworks\Faq\Model\Helpfulness\Manager;
use Aheadworks\Faq\Api\Data\VoteResultInterfaceFactory;
use Aheadworks\Faq\Api\Data\VoteResultInterface;

/**
 * Class HelpfulnessManagement
 * @package Aheadworks\Faq\Model
 */
class HelpfulnessManagement implements HelpfulnessManagementInterface
{
    /**
     * @var ArticleRepository
     */
    private $articleRepository;

    /**
     * @var Manager
     */
    private $helpfulnessManager;

    /**
     * var VoteResultInterfaceFactory
     */
    private $votesFactory;

    /**
     * @param ArticleRepository $articleRepository
     * @param Manager $helpfulnessManager
     * @param VoteResultInterfaceFactory $votesFactory
     */
    public function __construct(
        ArticleRepository $articleRepository,
        Manager $helpfulnessManager,
        VoteResultInterfaceFactory $votesFactory
    ) {
        $this->articleRepository = $articleRepository;
        $this->helpfulnessManager = $helpfulnessManager;
        $this->votesFactory = $votesFactory;
    }

    /**
     * @inheritDoc
     */
    public function like($articleId)
    {
        $article = $this->articleRepository->getById($articleId);

        if (!$this->helpfulnessManager->isSetAction(Manager::ACTION_LIKE, $articleId)) {
            if ($this->helpfulnessManager->isSetAction(Manager::ACTION_DISLIKE, $articleId)) {
                $this->helpfulnessManager->removeAction(Manager::ACTION_DISLIKE, $articleId);
                $article->setVotesNo($article->getVotesNo() - 1);
            }
            $article->setVotesYes($article->getVotesYes() + 1);
            $this->helpfulnessManager->addAction(Manager::ACTION_LIKE, $articleId);
        }

        $this->articleRepository->save($article);

        return $this->getVoteResult($articleId);
    }

    /**
     * @inheritDoc
     */
    public function dislike($articleId)
    {
        $article = $this->articleRepository->getById($articleId);

        if (!$this->helpfulnessManager->isSetAction(Manager::ACTION_DISLIKE, $articleId)) {
            if ($this->helpfulnessManager->isSetAction(Manager::ACTION_LIKE, $articleId)) {
                $this->helpfulnessManager->removeAction(Manager::ACTION_LIKE, $articleId);
                $article->setVotesYes($article->getVotesYes() - 1);
            }
            $article->setVotesNo($article->getVotesNo() + 1);
            $this->helpfulnessManager->addAction(Manager::ACTION_DISLIKE, $articleId);
        }

        $this->articleRepository->save($article);

        return $this->getVoteResult($articleId);
    }

    /**
     * Get vote result
     *
     * @param int $articleId
     * @return VoteResultInterface
     */
    private function getVoteResult($articleId)
    {
        /**
         * @var VoteResultInterface $voteResult
         */
        $voteResult = $this->votesFactory->create();
        $article = $this->articleRepository->getById($articleId);

        $likeStatus = $this->helpfulnessManager->isSetAction(Manager::ACTION_LIKE, $articleId);
        $dislikeStatus = $this->helpfulnessManager->isSetAction(Manager::ACTION_DISLIKE, $articleId);
        $helpfulnessRating = $article->getHelpfulnessRating();
        $voteResult
            ->setLikeStatus($likeStatus)
            ->setDislikeStatus($dislikeStatus)
            ->setHelpfulnessRating($helpfulnessRating);

        return $voteResult;
    }
}
