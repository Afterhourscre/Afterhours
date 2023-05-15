<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Faq\Controller\Article;

use Aheadworks\Faq\Controller\AbstractAction;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\App\Action\Context;
use Aheadworks\Faq\Api\HelpfulnessManagementInterface;
use Aheadworks\Faq\Model\Config;
use Aheadworks\Faq\Model\Url;
use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Helpfulness
 * @package Aheadworks\Faq\Controller\Article
 */
class Helpfulness extends AbstractAction
{
    /**
     * @var JsonFactory
     */
    private $resultJsonFactory;

    /**
     * @var HelpfulnessManagementInterface
     */
    private $helpfulnessManagement;

    /**
     * @var Url
     */
    private $url;

    /**
     * @param Url $url
     * @param Config $config
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param HelpfulnessManagementInterface $helpfulnessManagement
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Url $url,
        Config $config,
        Context $context,
        JsonFactory $resultJsonFactory,
        HelpfulnessManagementInterface $helpfulnessManagement,
        StoreManagerInterface $storeManager
    ) {
        parent::__construct($context, $storeManager);
        $this->url = $url;
        $this->config = $config;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->helpfulnessManagement = $helpfulnessManagement;
    }

    /**
     * Like\Dislike action
     *
     * @return Json
     */
    public function _execute()
    {
        $data = [];
        $params = json_decode($this->getRequest()->getContent(), true);
        $articleId = isset($params['articleId']) ? $params['articleId'] : null;
        $like = isset($params['isLike']) ? $params['isLike'] : null;
        $success = false;

        try {
            $voteResult = $like
                ? $this->helpfulnessManagement->like($articleId)
                : $this->helpfulnessManagement->dislike($articleId);
            $success = true;
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage(__('Something went wrong. Please vote again later.'));
            $data['success'] = $success;
            return $this->resultJsonFactory->create()->setData($data);
        }

        $data = [
            'success' => $success,
            'like' => $voteResult->getLikeStatus(),
            'dislike' => $voteResult->getDislikeStatus(),
            'helpfulness_rating' => $voteResult->getHelpfulnessRating()
        ];

        $this->messageManager->addSuccessMessage(__('Thank you for voting!'));

        return $this->resultJsonFactory->create()->setData($data);
    }
}
