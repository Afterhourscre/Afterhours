<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Acr\Model;

use Aheadworks\Acr\Api\Data\RuleInterface;
use Aheadworks\Acr\Api\Data\CartHistoryInterface;
use Aheadworks\Acr\Api\RuleManagementInterface;
use Aheadworks\Acr\Api\Data\RuleSearchResultsInterface;
use Aheadworks\Acr\Api\Data\RuleSearchResultsInterfaceFactory;
use Aheadworks\Acr\Api\RuleRepositoryInterface;
use Aheadworks\Acr\Model\Source\Rule\Status as RuleStatus;
use Aheadworks\Acr\Model\Rule\Validator as RuleValidator;
use Aheadworks\Acr\Api\Data\PreviewInterface;
use Aheadworks\Acr\Api\Data\PreviewInterfaceFactory;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\Api\SearchCriteriaBuilder;

/**
 * Class RuleManagement
 * @package Aheadworks\Acr\Model
 */
class RuleManagement implements RuleManagementInterface
{
    /**
     * @var DateTime
     */
    private $dateTime;

    /**
     * @var RuleSearchResultsInterfaceFactory
     */
    private $searchResultsFactory;

    /**
     * @var RuleRepositoryInterface
     */
    private $ruleRepository;

    /**
     * @var RuleValidator
     */
    private $ruleValidator;

    /**
     * @var Sender
     */
    private $sender;

    /**
     * @var PreviewInterfaceFactory
     */
    private $previewFactory;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @param DateTime $dateTime
     * @param RuleSearchResultsInterfaceFactory $searchResultsFactory
     * @param RuleRepositoryInterface $ruleRepository
     * @param RuleValidator $ruleValidator
     * @param Sender $sender
     * @param PreviewInterfaceFactory $previewFactory
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        DateTime $dateTime,
        RuleSearchResultsInterfaceFactory $searchResultsFactory,
        RuleRepositoryInterface $ruleRepository,
        RuleValidator $ruleValidator,
        Sender $sender,
        PreviewInterfaceFactory $previewFactory,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->dateTime = $dateTime;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->ruleRepository = $ruleRepository;
        $this->ruleValidator = $ruleValidator;
        $this->sender = $sender;
        $this->previewFactory = $previewFactory;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function validate(CartHistoryInterface $cartHistory)
    {
        $this->searchCriteriaBuilder
            ->addFilter(RuleInterface::STATUS, RuleStatus::ENABLED, 'eq');

        $rules = $this->ruleRepository
            ->getList($this->searchCriteriaBuilder->create())
            ->getItems();

        $validatedRules = [];
        $cartData = json_decode($cartHistory->getCartData(), true);

        /** @var RuleInterface $rule */
        foreach ($rules as $rule) {
            if ($this->ruleValidator->validate($rule, $cartData)) {
                $validatedRules[] = $rule;
            }
        }

        /** @var RuleSearchResultsInterface $result */
        $result = $this->searchResultsFactory->create();

        $result
            ->setItems($validatedRules)
            ->setTotalCount(count($validatedRules));

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function getEmailSendTime(RuleInterface $rule, $triggerTime)
    {
        $sendDate = $this->dateTime->timestamp($triggerTime) + $this->getDeltaTimestamp($rule);
        return $this->dateTime->date(null, $sendDate);
    }

    /**
     * {@inheritdoc}
     */
    public function getPreview($storeId, $subject, $content)
    {
        /** @var array $previewContent */
        $previewContent = $this->sender->getTestPreview($storeId, $subject, $content);
        /** @var PreviewInterface $preview */
        $preview = $this->previewFactory->create();
        $preview
            ->setStoreId($storeId)
            ->setRecipientName($previewContent['recipient_name'])
            ->setRecipientEmail($previewContent['recipient_email'])
            ->setSubject($previewContent['subject'])
            ->setContent($previewContent['content']);

        return $preview;
    }

    /**
     * Get delta timestamp
     * @param RuleInterface $rule
     * @return int
     */
    private function getDeltaTimestamp(RuleInterface $rule)
    {
        return 60 * ($rule->getEmailSendMinutes() + 60 * ($rule->getEmailSendHours() + $rule->getEmailSendDays() * 24));
    }
}
