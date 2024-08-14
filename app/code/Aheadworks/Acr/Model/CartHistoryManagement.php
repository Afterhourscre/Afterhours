<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Acr\Model;

use Aheadworks\Acr\Api\Data\CartHistoryInterface;
use Aheadworks\Acr\Api\Data\CartHistoryInterfaceFactory;
use Aheadworks\Acr\Api\Data\CartRestoreInterfaceFactory;
use Aheadworks\Acr\Api\CartHistoryManagementInterface;
use Aheadworks\Acr\Api\CartRestoreManagementInterface;
use Aheadworks\Acr\Api\CartHistoryRepositoryInterface;
use Aheadworks\Acr\Api\CartRestoreRepositoryInterface;
use Aheadworks\Acr\Api\QueueRepositoryInterface;
use Aheadworks\Acr\Api\QueueManagementInterface;
use Aheadworks\Acr\Api\RuleManagementInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Psr\Log\LoggerInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\App\Emulation as AppEmulation;

/**
 * Class CartHistoryManagement
 * @package Aheadworks\Acr\Model
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class CartHistoryManagement implements CartHistoryManagementInterface
{
    /**
     * @var CartHistoryInterfaceFactory
     */
    private $cartHistoryFactory;

    /**
     * @var CartRestoreInterfaceFactory
     */
    private $cartRestoreFactory;

    /**
     * @var CartHistoryRepositoryInterface
     */
    private $cartHistoryRepository;

    /**
     * @var CartRestoreRepositoryInterface
     */
    private $cartRestoreRepository;

    /**
     * @var QueueRepositoryInterface
     */
    private $queueRepository;

    /**
     * @var QueueManagementInterface
     */
    private $queueManagement;

    /**
     * @var RuleManagementInterface
     */
    private $ruleManagement;

    /**
     * @var CartRestoreManagementInterface
     */
    private $cartRestoreManagement;

    /**
     * @var CartRepositoryInterface
     */
    private $cartRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var DateTime
     */
    private $dateTime;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var AppEmulation
     */
    private $appEmulation;

    /**
     * @param CartHistoryInterfaceFactory $cartHistoryFactory
     * @param CartRestoreInterfaceFactory $cartRestoreFactory
     * @param CartHistoryRepositoryInterface $cartHistoryRepository
     * @param CartRestoreRepositoryInterface $cartRestoreRepository
     * @param QueueRepositoryInterface $queueRepository
     * @param QueueManagementInterface $queueManagement
     * @param RuleManagementInterface $ruleManagement
     * @param CartRepositoryInterface $cartRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param DateTime $dateTime
     * @param LoggerInterface $logger
     * @param AppEmulation $appEmulation
     * @param CartRestoreManagementInterface $cartRestoreManagement
     */
    public function __construct(
        CartHistoryInterfaceFactory $cartHistoryFactory,
        CartRestoreInterfaceFactory $cartRestoreFactory,
        CartHistoryRepositoryInterface $cartHistoryRepository,
        CartRestoreRepositoryInterface $cartRestoreRepository,
        QueueRepositoryInterface $queueRepository,
        QueueManagementInterface $queueManagement,
        RuleManagementInterface $ruleManagement,
        CartRepositoryInterface $cartRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        DateTime $dateTime,
        LoggerInterface $logger,
        AppEmulation $appEmulation,
        CartRestoreManagementInterface $cartRestoreManagement
    ) {
        $this->cartHistoryFactory = $cartHistoryFactory;
        $this->cartRestoreFactory = $cartRestoreFactory;
        $this->cartHistoryRepository = $cartHistoryRepository;
        $this->cartRestoreRepository = $cartRestoreRepository;
        $this->queueRepository = $queueRepository;
        $this->queueManagement = $queueManagement;
        $this->ruleManagement = $ruleManagement;
        $this->cartRepository = $cartRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->dateTime = $dateTime;
        $this->logger = $logger;
        $this->appEmulation = $appEmulation;
        $this->cartRestoreManagement = $cartRestoreManagement;
    }

    /**
     * {@inheritdoc}
     */
    public function process(CartHistoryInterface $cartHistory)
    {
        try {
            $cartData = json_decode($cartHistory->getCartData(), true);
            $storeId = $cartData['store_id'];
            $this->appEmulation->startEnvironmentEmulation($storeId, 'frontend', true);
            $cart = $this->cartRepository->get($cartHistory->getReferenceId());
            $this->appEmulation->stopEnvironmentEmulation();
            if (!$cart->getIsActive()
                || $cart->getItemsCount() == 0
            ) {
                $this->cartHistoryRepository->delete($cartHistory);
            } else {
                $result = $this->ruleManagement->validate($cartHistory);
                if ($result->getTotalCount() > 0) {
                    foreach ($result->getItems() as $rule) {
                        $this->queueManagement->add($rule, $cartHistory);
                    }
                    $cartHistory->setProcessed(true);
                    $this->cartHistoryRepository->save($cartHistory);
                } else {
                    $this->cartHistoryRepository->delete($cartHistory);
                }
            }
        } catch (NoSuchEntityException $e) {
            $this->cartHistoryRepository->delete($cartHistory);
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function processUnprocessedItems($maxItemsCount)
    {
        $this->searchCriteriaBuilder
            ->addFilter(CartHistoryInterface::PROCESSED, false)
            ->setPageSize($maxItemsCount);
        $result = $this->cartHistoryRepository->getList(
            $this->searchCriteriaBuilder->create()
        );

        $cartHistoryItems = $result->getItems();
        foreach ($cartHistoryItems as $cartHistoryItem) {
            try {
                $triggerAt = $this->dateTime->timestamp($cartHistoryItem->getTriggeredAt());
                $now = $this->dateTime->timestamp();
                if ($now - $triggerAt > self::CART_TRIGGER_TIMEOUT) {
                    $this->process($cartHistoryItem);
                }
            } catch (\Exception $e) {
                $this->logger->error($e);
            }
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function addCartToCartHistory($cartData)
    {
        if (!$this->validateCartData($cartData)) {
            return false;
        }

        $this->searchCriteriaBuilder
            ->addFilter(CartHistoryInterface::REFERENCE_ID, $cartData['entity_id']);

        $result = $this->cartHistoryRepository->getList(
            $this->searchCriteriaBuilder->create()
        );
        $cartHistoryItems = $result->getItems();

        $cartHistory = reset($cartHistoryItems);
        if ($cartHistory) {
            if ($cartHistory->getProcessed()) {
                $this->queueRepository->deleteByCartHistoryId($cartHistory->getId());
                $this->cartHistoryRepository->delete($cartHistory);
                $cartHistory = $this->cartHistoryFactory->create();
            }
        } else {
            $cartHistory = $this->cartHistoryFactory->create();
        }

        $cartHistory
            ->setReferenceId($cartData['entity_id'])
            ->setCartData($this->getPreparedCartData($cartData))
            ->setTriggeredAt($this->dateTime->date());

        $this->cartHistoryRepository->save($cartHistory);
        $this->cartRestoreManagement->saveRestoreCode($cartHistory->getId(), $cartData['entity_id']);

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getCartIdByEventHistoryId($eventHistoryId)
    {
        $this->searchCriteriaBuilder
            ->addFilter(CartHistoryInterface::ID, $eventHistoryId, 'eq');

        $result = $this->cartHistoryRepository->getList(
            $this->searchCriteriaBuilder->create()
        );

        foreach ($result->getItems() as $item) {
            return $cartId = $item->getReferenceId();
        }
    }

    /**
     * Validation of cart data
     *
     * @param array $data
     * @return bool
     */
    private function validateCartData(array $data)
    {
        $dataKeysRequired = ['email', 'store_id', 'customer_group_id', 'customer_name'];
        foreach ($dataKeysRequired as $dataKey) {
            if (!array_key_exists($dataKey, $data)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Get prepared cart data
     *
     * @param array $data
     * @return string
     */
    private function getPreparedCartData(array $data)
    {
        foreach ($data as $key => $value) {
            if ((is_array($value) || is_object($value))) {
                unset($data[$key]);
            }

            if (isset($data[$key]) && preg_match("/\r\n|\r|\n/", $value)) {
                $data[$key] = preg_replace("/\r\n|\r|\n/", "", $value);
            }
        }
        return json_encode($data);
    }
}
