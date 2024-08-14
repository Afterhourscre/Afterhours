<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Acr\Model;

use Aheadworks\Acr\Api\Data\CartRestoreInterfaceFactory;
use Aheadworks\Acr\Api\CartRestoreRepositoryInterface;
use Aheadworks\Acr\Api\CartRestoreManagementInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;

/**
 * Class CartRestoreManagement
 * @package Aheadworks\Acr\Model
 */
class CartRestoreManagement implements CartRestoreManagementInterface
{
    /**
     * @var CartRestoreInterfaceFactory
     */
    private $cartRestoreFactory;

    /**
     * @var CartRestoreRepositoryInterface
     */
    private $cartRestoreRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @param CartRestoreInterfaceFactory $cartRestoreFactory
     * @param CartRestoreRepositoryInterface $cartRestoreRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        CartRestoreInterfaceFactory $cartRestoreFactory,
        CartRestoreRepositoryInterface $cartRestoreRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->cartRestoreFactory = $cartRestoreFactory;
        $this->cartRestoreRepository = $cartRestoreRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function saveRestoreCode($cartHistoryId, $quoteId)
    {
        try {
            $cartRestore = $this->cartRestoreRepository->getByEventHistoryId($cartHistoryId);
            if (!$cartRestore) {
                $cartRestore = $this->cartRestoreFactory->create();
                $cartRestore
                    ->setEventHistoryId($cartHistoryId)
                    ->setRestoreCode($this->generateHashCode($quoteId));

                $this->cartRestoreRepository->save($cartRestore);
            }
        } catch (NoSuchEntityException $e) {
            //show error
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getCartRestoreItemByHistoryId($eventHistoryId)
    {
        return $cartRestore = $this->cartRestoreRepository->getByEventHistoryId($eventHistoryId);
    }

    /**
     * Generate hash code
     *
     * @param string|int $value
     * @return string
     */
    private function generateHashCode($value)
    {
        return hash('sha512', $value);
    }
}
