<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Acr\Model\Template\VariableProcessor;

use Aheadworks\Acr\Api\Data\CartHistoryInterface;
use Magento\Store\Model\StoreManagerInterface;
use Aheadworks\Acr\Model\Email\UrlBuilder;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Aheadworks\Acr\Api\CartRestoreManagementInterface;
use Aheadworks\Acr\Api\CartRestoreRepositoryInterface;
use Aheadworks\Acr\Api\CartHistoryRepositoryInterface;
use Aheadworks\Acr\Model\Source\Email\Variables;

/**
 * Class CartRestoreLink
 *
 * @package Aheadworks\Acr\Model\Template\VariableProcessor
 */
class CartRestoreLink implements VariableProcessorInterface
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var UrlBuilder
     */
    private $urlBuilder;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var CartRestoreRepositoryInterface
     */
    private $cartRestoreRepository;

    /**
     * @var CartHistoryRepositoryInterface
     */
    private $cartHistoryRepository;

    /**
     * @var CartRestoreManagementInterface
     */
    private $cartRestoreManagement;

    /**
     * @param StoreManagerInterface $storeManager
     * @param UrlBuilder $urlBuilder
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param CartRestoreRepositoryInterface $cartRestoreRepository
     * @param CartHistoryRepositoryInterface $cartHistoryRepository
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        UrlBuilder $urlBuilder,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        CartRestoreRepositoryInterface $cartRestoreRepository,
        CartHistoryRepositoryInterface $cartHistoryRepository,
        CartRestoreManagementInterface $cartRestoreManagement
    ) {
        $this->storeManager = $storeManager;
        $this->urlBuilder = $urlBuilder;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->cartRestoreRepository = $cartRestoreRepository;
        $this->cartHistoryRepository = $cartHistoryRepository;
        $this->cartRestoreManagement = $cartRestoreManagement;
    }

    /**
     * {@inheritdoc}
     */
    public function process($quote, $params)
    {
        $this->searchCriteriaBuilder
            ->addFilter(CartHistoryInterface::REFERENCE_ID, $quote->getEntityId());
        $result = $this->cartHistoryRepository->getList(
            $this->searchCriteriaBuilder->create()
        );
        foreach ($result->getItems() as $item) {
            $cartRestore = $this->cartRestoreManagement->getCartRestoreItemByHistoryId($item->getId());
        }
        if ($cartRestore) {
            return [Variables::CART_RESTORE_LINK => $this->urlBuilder
                ->getUrl(
                    'aw_acr/cart/restore',
                    $this->storeManager->getStore($quote->getCustomer()->getStoreId()),
                    [
                        'code' => $cartRestore->getRestoreCode(),
                        '_scope_to_url' => true,
                    ],
                    'frontend'
                )];
        } else {
            return [Variables::CART_RESTORE_LINK => ''];
        }
    }

    /**
     * {@inheritdoc}
     */
    public function processTest($params)
    {
        return [Variables::CART_RESTORE_LINK => $this->urlBuilder
            ->getUrl(
                'aw_acr/cart/restore',
                $this->storeManager->getStore($params['store_id']),
                [
                    'code' => 'test',
                    '_scope_to_url' => true,
                ],
                'frontend'
            )];
    }
}
