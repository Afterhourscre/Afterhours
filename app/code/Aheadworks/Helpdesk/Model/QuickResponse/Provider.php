<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Helpdesk\Model\QuickResponse;

use Aheadworks\Helpdesk\Api\Data\QuickResponseInterface;
use Aheadworks\Helpdesk\Api\QuickResponseRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Api\SearchCriteriaBuilder;

/**
 * Class Provider
 * @package Aheadworks\Helpdesk\Model\QuickResponse
 */
class Provider
{
    /**
     *  Maximum length for quick response title
     */
    const TITLE_MAX_LENGTH = 128;

    /**
     * @var QuickResponseRepositoryInterface
     */
    private $quickResponseRepository;

    /**
     * @var StoreValueResolver
     */
    private $storeValueResolver;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param QuickResponseRepositoryInterface $quickResponseRepository
     * @param StoreValueResolver $storeValueResolver
     */
    public function __construct(
        SearchCriteriaBuilder $searchCriteriaBuilder,
        QuickResponseRepositoryInterface $quickResponseRepository,
        StoreValueResolver $storeValueResolver
    ) {
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->quickResponseRepository = $quickResponseRepository;
        $this->storeValueResolver = $storeValueResolver;
    }
    /**
     * Returns quick response array by store id
     *
     * @param int $storeId
     * @return array
     * @throws LocalizedException
     */
    public function getQuickResponsesValues($storeId)
    {
        $quickResponseValues = [];
        $quickResponses = $this->getActiveQuickResponses();
        foreach ($quickResponses as $quickResponse) {
            $responseText = $this->storeValueResolver->getValueByStoreId(
                $quickResponse->getStoreResponseValues(),
                $storeId
            );
            if ($responseText) {
                $quickResponseValues[] = [
                    'store_id' => $storeId,
                    'id' => $quickResponse->getId(),
                    'title' => $this->prepareTitle($quickResponse->getTitle()),
                    'value' => $responseText
                ];
            }
        }

        return $quickResponseValues;
    }

    /**
     * Prepare title for quick response
     *
     * @param string $title
     * @return string
     */
    private function prepareTitle($title)
    {
        return strlen($title) <= self::TITLE_MAX_LENGTH
            ? $title
            : substr($title, 0, self::TITLE_MAX_LENGTH) . '...';
    }

    /**
     * Retrieve enabled quick responses
     *
     * @return QuickResponseInterface[]
     * @throws LocalizedException
     */
    private function getActiveQuickResponses()
    {
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter(QuickResponseInterface::IS_ACTIVE, true, 'eq')
            ->create();

        return $this->quickResponseRepository->getList($searchCriteria)->getItems();
    }
}
