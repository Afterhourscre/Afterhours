<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Faq\Model\Layout\Processor;

use Magento\Framework\Stdlib\ArrayManager;
use Aheadworks\Faq\Api\Data\ArticleInterface;
use Aheadworks\Faq\Model\Config;
use Aheadworks\Faq\Model\Helpfulness\Manager;
use Magento\Customer\Model\Group;
use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Customer\Model\Context as CustomerContext;

/**
 * Class HelpfulnessProcessor
 * @package Aheadworks\Faq\Model\Layout\Processor
 */
class HelpfulnessProcessor implements LayoutProcessorInterface
{
    /**
     * @var ArrayManager
     */
    private $arrayManager;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var Manager
     */
    private $helpfulnessManager;

    /**
     * @var HttpContext
     */
    private $httpContext;

    /**
     * @param ArrayManager $arrayManager
     * @param Config $config
     * @param Manager $helpfulnessManager
     * @param HttpContext $httpContext
     */
    public function __construct(
        ArrayManager $arrayManager,
        Config $config,
        Manager $helpfulnessManager,
        HttpContext $httpContext
    ) {
        $this->arrayManager = $arrayManager;
        $this->config = $config;
        $this->helpfulnessManager = $helpfulnessManager;
        $this->httpContext = $httpContext;
    }

    /**
     * {@inheritdoc}
     */
    public function process($jsLayout, $article)
    {
        $jsLayout = $this->processConfigData($jsLayout, $article);
        $jsLayout = $this->processProviderData($jsLayout, $article);

        return $jsLayout;
    }

    private function processConfigData($jsLayout, $article)
    {
        $path = 'components/awFaqVoting/config';
        $jsLayout = $this->arrayManager->merge(
            $path,
            $jsLayout,
            $this->getVotingConfigData($article)
        );

        return $jsLayout;
    }

    /**
     * Process provider data
     *
     * @param array $jsLayout
     * @param ArticleInterface $article
     * @return array
     */
    private function processProviderData($jsLayout, $article)
    {
        $path = 'components/awFaqVotingProvider';
        $jsLayout = $this->arrayManager->merge(
            $path,
            $jsLayout,
            [
                'data' => [
                    'isVoted' => $this->isVoted($article),
                    'isVoteLike' => $this->getLikeStatus($article),
                    'helpfulnessRating' => $article->getHelpfulnessRating()
                ]
            ]
        );

        return $jsLayout;
    }

    /**
     * Retrieve voting config data
     *
     * @param ArticleInterface $article
     * @return array
     */
    private function getVotingConfigData($article)
    {
        $votesYes = $article->getVotesYes();
        $votesNo = $article->getVotesNo();

        $preparedData = [
            'canDisplay' => $this->isAllowedHelpfulness(),
            'isRateBeforeVotingEnabled' => $this->isHelpfulnessRateBeforeVotingEnabled(),
            'isRateAfterVotingEnabled' => $this->isHelpfulnessRateAfterVotingEnabled(),
            'canDisplayRatingMessage' => ($votesYes + $votesNo > 0) ? true : false,
            'articleId' => $article->getArticleId()
        ];

        return $preparedData;
    }

    /**
     * Check is allowed for helpfulness
     *
     * @return bool
     */
    private function isAllowedHelpfulness()
    {
        $customerGroups = $this->config->getDefaultCustomerGroupsToDisplayHelpfulness();
        if (in_array(Group::CUST_GROUP_ALL, $customerGroups)) {
            return true;
        }

        $currentCustomerGroup = $this->httpContext->getValue(CustomerContext::CONTEXT_GROUP);
        if (in_array($currentCustomerGroup, $customerGroups)) {
            return true;
        }

        return false;
    }

    /**
     * Check if user already voted
     *
     * @param ArticleInterface $article
     * @return bool
     */
    private function isVoted($article)
    {
        if ($this->getLikeStatus($article) || $this->getDislikeStatus($article)) {
            return true;
        }
        return false;
    }

    /**
     * Get like status
     *
     * @param ArticleInterface $article
     * @return string
     */
    private function getLikeStatus($article)
    {
        return $this->helpfulnessManager->isSetAction(
            Manager::ACTION_LIKE,
            $article->getArticleId()
        );
    }

    /**
     * Get dislike status
     *
     * @param ArticleInterface $article
     * @return string
     */
    private function getDislikeStatus($article)
    {
        return $this->helpfulnessManager->isSetAction(
            Manager::ACTION_DISLIKE,
            $article->getArticleId()
        );
    }

    /**
     * Checks if helpfulness rate is enabled before voting
     *
     * @return bool
     */
    private function isHelpfulnessRateBeforeVotingEnabled()
    {
        return $this->config->isHelpfulnessRateBeforeVotingEnabled();
    }

    /**
     * Checks if helpfulness rate is enabled after voting
     *
     * @return bool
     */
    private function isHelpfulnessRateAfterVotingEnabled()
    {
        return $this->config->isHelpfulnessRateAfterVotingEnabled();
    }
}
