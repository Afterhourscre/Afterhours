<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Acr\Model;

use Aheadworks\Acr\Api\Data\QueueInterface;
use Aheadworks\Acr\Api\Data\QueueInterfaceFactory;
use Aheadworks\Acr\Api\Data\QueueSearchResultsInterface;
use Aheadworks\Acr\Api\Data\CartHistoryInterface;
use Aheadworks\Acr\Api\CartHistoryRepositoryInterface;
use Aheadworks\Acr\Api\Data\RuleInterface;
use Aheadworks\Acr\Api\RuleManagementInterface;
use Aheadworks\Acr\Api\RuleRepositoryInterface;
use Aheadworks\Acr\Api\QueueManagementInterface;
use Aheadworks\Acr\Api\QueueRepositoryInterface;
use Aheadworks\Acr\Api\Data\PreviewInterface;
use Aheadworks\Acr\Api\Data\PreviewInterfaceFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\MailException;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Store\Model\App\Emulation as AppEmulation;
use Aheadworks\Acr\Model\Template\PreviewFilter;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\Store;

/**
 * Class QueueManagement
 * @package Aheadworks\Acr\Model
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class QueueManagement implements QueueManagementInterface
{
    /**
     * @var QueueInterfaceFactory
     */
    private $queueFactory;

    /**
     * @var QueueRepositoryInterface
     */
    private $queueRepository;

    /**
     * @var CartHistoryRepositoryInterface
     */
    private $cartHistoryRepository;

    /**
     * @var RuleManagementInterface
     */
    private $ruleManagement;

    /**
     * @var RuleRepositoryInterface
     */
    private $ruleRepository;

    /**
     * @var PreviewInterfaceFactory
     */
    private $previewFactory;

    /**
     * @var Sender
     */
    private $sender;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var DateTime
     */
    private $dateTime;

    /**
     * @var AppEmulation
     */
    private $appEmulation;

    /**
     * @var PreviewFilter
     */
    private $previewFilter;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param QueueInterfaceFactory $queueFactory
     * @param QueueRepositoryInterface $queueRepository
     * @param CartHistoryRepositoryInterface $cartHistoryRepository
     * @param RuleManagementInterface $ruleManagement
     * @param RuleRepositoryInterface $ruleRepository
     * @param PreviewInterfaceFactory $previewFactory
     * @param Sender $sender
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param DateTime $dateTime
     * @param AppEmulation $appEmulation
     * @param PreviewFilter $previewFilter
     * @param StoreManagerInterface $storeManager
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        QueueInterfaceFactory $queueFactory,
        QueueRepositoryInterface $queueRepository,
        CartHistoryRepositoryInterface $cartHistoryRepository,
        RuleManagementInterface $ruleManagement,
        RuleRepositoryInterface $ruleRepository,
        PreviewInterfaceFactory $previewFactory,
        Sender $sender,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        DateTime $dateTime,
        AppEmulation $appEmulation,
        PreviewFilter $previewFilter,
        StoreManagerInterface $storeManager
    ) {
        $this->queueFactory = $queueFactory;
        $this->queueRepository = $queueRepository;
        $this->cartHistoryRepository = $cartHistoryRepository;
        $this->ruleManagement = $ruleManagement;
        $this->ruleRepository = $ruleRepository;
        $this->previewFactory = $previewFactory;
        $this->sender = $sender;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->dateTime = $dateTime;
        $this->appEmulation = $appEmulation;
        $this->previewFilter = $previewFilter;
        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function add(RuleInterface $rule, CartHistoryInterface $cartHistory)
    {
        $cartData = json_decode($cartHistory->getCartData(), true);
        /** @var QueueInterface $queue */
        $queue = $this->queueFactory->create();
        $queue
            ->setRuleId($rule->getId())
            ->setStatus(QueueInterface::STATUS_PENDING)
            ->setScheduledAt($this->ruleManagement->getEmailSendTime($rule, $this->dateTime->date()))
            ->setStoreId($cartData['store_id'])
            ->setRecipientName($cartData['customer_name'])
            ->setRecipientEmail($cartData['email'])
            ->setCartHistoryId($cartHistory->getId());
        try {
            $this->queueRepository->save($queue);
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function cancel(QueueInterface $queue)
    {
        if ($this->canCancel($queue)) {
            $queue->setStatus(QueueInterface::STATUS_CANCELLED);
            $this->queueRepository->save($queue);
            return true;
        }
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function cancelById($queueId)
    {
        try {
            $queue = $this->queueRepository->get($queueId);
            $result = $this->cancel($queue);
            return $result;
        } catch (NoSuchEntityException $e) {
            throw new LocalizedException(__('Unable to cancel.'));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function send(QueueInterface $queue)
    {
        try {
            $this->appEmulation->startEnvironmentEmulation($queue->getStoreId(), 'frontend', true);
            $queue = $this->sender->sendQueueItem($queue);
            $this->appEmulation->stopEnvironmentEmulation();
            $queue->setStatus(QueueInterface::STATUS_SENT);
            $queue->setSentAt($this->dateTime->date());
            $this->queueRepository->save($queue);
            return true;
        } catch (MailException $e) {
            $this->appEmulation->stopEnvironmentEmulation();
            $queue->setStatus(QueueInterface::STATUS_FAILED);
            $this->queueRepository->save($queue);
            return false;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function sendTest(RuleInterface $rule)
    {
        $storeIds = $rule->getStoreIds();
        $storeId = reset($storeIds);
        if ($storeId == Store::DEFAULT_STORE_ID) {
            $storeId = $this->storeManager->getDefaultStoreView()->getId();
        }
        /** @var QueueInterface $queue */
        $queue = $this->queueFactory->create();
        $queue
            ->setRuleId($rule->getId())
            ->setStoreId($storeId);
        try {
            $queue = $this->sender->sendTestEmail($queue);

            $date = $this->dateTime->date();
            $queue
                ->setStatus(QueueInterface::STATUS_SENT)
                ->setScheduledAt($date)
                ->setSentAt($date)
                ->setCartHistoryId(0);
            $this->queueRepository->save($queue);
            return true;
        } catch (MailException $e) {
            $queue->setStatus(QueueInterface::STATUS_FAILED);
            $this->queueRepository->save($queue);
            return false;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function sendById($queueId)
    {
        try {
            $queue = $this->queueRepository->get($queueId);
            $result = $this->send($queue);
            return $result;
        } catch (NoSuchEntityException $e) {
            throw new LocalizedException(__('Unable to send.'));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getPreview(QueueInterface $queue)
    {
        /** @var PreviewInterface $preview */
        $preview = $this->previewFactory->create();
        $preview
            ->setStoreId($queue->getStoreId())
            ->setRecipientName($queue->getRecipientName())
            ->setRecipientEmail($queue->getRecipientEmail());

        if ($queue->getSavedContent()) {
            $preview
                ->setSubject($queue->getSavedSubject())
                ->setContent($this->previewFilter->filter($queue->getSavedContent()));
        } else {
            /** @var RuleInterface $rule */
            $rule = $this->ruleRepository->get($queue->getRuleId());
            $previewContent = $this->sender->getPreview($queue, $rule->getSubject(), $rule->getContent());
            $preview
                ->setSubject($previewContent['subject'])
                ->setContent($this->previewFilter->filter($previewContent['content']));
        }
        return $preview;
    }

    /**
     * {@inheritdoc}
     */
    public function clearQueue($keepForDays)
    {
        if ($keepForDays) {
            $expirationDate = date(
                \Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT,
                strtotime("-" . $keepForDays . " days")
            );

            $this->searchCriteriaBuilder
                ->addFilter(QueueInterface::SCHEDULED_AT, $expirationDate, 'lteq');

            /** @var QueueSearchResultsInterface $result */
            $result = $this->queueRepository->getList(
                $this->searchCriteriaBuilder->create()
            );

            /** @var QueueInterface $queueItem */
            foreach ($result->getItems() as $queueItem) {
                $this->queueRepository->delete($queueItem);
            }
            return true;
        }
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function sendScheduledEmails($timestamp = null)
    {
        $now = date(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT, $this->dateTime->timestamp());

        $this->searchCriteriaBuilder
            ->addFilter(QueueInterface::STATUS, QueueInterface::STATUS_PENDING, 'eq')
            ->addFilter(QueueInterface::SCHEDULED_AT, $now, 'lteq');

        /** @var QueueSearchResultsInterface $result */
        $result = $this->queueRepository->getList(
            $this->searchCriteriaBuilder->create()
        );

        /** @var QueueInterface $queueItem */
        foreach ($result->getItems() as $queueItem) {
            try {
                $this->send($queueItem);
            } catch (\Exception $e) {
                //add exception to log file
            }
        }
        return true;
    }

    /**
     * Check if queue can be cancelled
     *
     * @param QueueInterface $queue
     * @return bool
     */
    private function canCancel(QueueInterface $queue)
    {
        if ($queue->getStatus() == QueueInterface::STATUS_PENDING) {
            return true;
        }
        return false;
    }
}
