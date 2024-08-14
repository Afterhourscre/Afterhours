<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Faq\Model\Helpfulness;

use Magento\Customer\Model\Visitor;
use Aheadworks\Faq\Model\ResourceModel\Votes as VotesResource;
use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Customer\Model\Context as CustomerContext;
use Magento\Customer\Helper\Session\CurrentCustomer;
use Magento\Framework\Registry;

/**
 * Class Manager
 * @package Aheadworks\Faq\Model\Helpfulness
 */
class Manager
{
    const ACTION_LIKE = 'like';
    const ACTION_DISLIKE = 'dislike';

    /**
     * @var HttpContext
     */
    private $httpContext;

    /**
     * @var CurrentCustomer
     */
    private $currentCustomer;

    /**
     * @var Visitor
     */
    private $visitor;

    /**
     * @var VotesResource
     */
    private $votesResource;

    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var array
     */
    private $actions = [];

    /**
     * @param HttpContext $httpContext
     * @param Visitor $visitor
     * @param VotesResource $votesResource
     * @param CurrentCustomer $currentCustomer
     * @param Registry $registry
     */
    public function __construct(
        HttpContext $httpContext,
        Visitor $visitor,
        VotesResource $votesResource,
        CurrentCustomer $currentCustomer,
        Registry $registry
    ) {
        $this->visitor = $visitor;
        $this->votesResource = $votesResource;
        $this->httpContext = $httpContext;
        $this->currentCustomer = $currentCustomer;
        $this->registry = $registry;
    }

    /**
     * Add vote action
     *
     * @param string $action
     * @param int $articleId
     * @return $this
     */
    public function addAction($action, $articleId)
    {
        $this->cleanActionsCached($action, $articleId);
        if ($this->httpContext->getValue(CustomerContext::CONTEXT_AUTH)) {
            $this->votesResource->addCustomerAction(
                $this->getCustomerId(),
                $articleId,
                $action
            );
        } else {
            $this->votesResource->addVisitorAction(
                $this->getVisitorId(),
                $articleId,
                $action
            );
        }

        return $this;
    }

    /**
     * Remove vote action
     *
     * @param string $action
     * @param int $articleId
     * @return $this
     */
    public function removeAction($action, $articleId)
    {
        $this->cleanActionsCached($action, $articleId);
        if ($this->httpContext->getValue(CustomerContext::CONTEXT_AUTH)) {
            $this->votesResource->removeCustomerAction(
                $this->getCustomerId(),
                $articleId,
                $action
            );
        } else {
            $this->votesResource->removeVisitorAction(
                $this->getVisitorId(),
                $articleId,
                $action
            );
        }
        return $this;
    }

    /**
     * Check vote status
     *
     * @param string $action
     * @param int $articleId
     * @return bool
     */
    public function isSetAction($action, $articleId)
    {
        $key = $action . '-' . $articleId;
        if (!isset($this->actions[$key])) {
            if ($this->httpContext->getValue(CustomerContext::CONTEXT_AUTH)) {
                $this->actions[$key] = $this->votesResource->isSetCustomerAction(
                    $this->getCustomerId(),
                    $articleId,
                    $action
                );
            } else {
                $this->actions[$key] = $this->votesResource->isSetVisitorAction(
                    $this->getVisitorId(),
                    $articleId,
                    $action
                );
            }
        }

        return $this->actions[$key];
    }

    /**
     * Clean actions cached
     *
     * @param string $action
     * @param int $articleId
     * @return $this
     */
    private function cleanActionsCached($action, $articleId)
    {
        $key = $action . '-' . $articleId;
        if (isset($this->actions[$key])) {
            unset($this->actions[$key]);
        }

        return $this;
    }

    /**
     * Retrieve customer id
     *
     * @return int|null
     */
    private function getCustomerId()
    {
        $customerId = $this->currentCustomer->getCustomerId();
        if (!$customerId) {
            $customerId = $this->registry->registry('aw_faq_customer_id');
        }

        return $customerId;
    }

    /**
     * Retrieve visitor id
     *
     * @return int|null
     */
    private function getVisitorId()
    {
        $visitorId = $this->visitor->getId();
        if (!$visitorId) {
            $visitorId = $this->registry->registry('aw_faq_visitor_id');
        }

        return $visitorId;
    }
}
