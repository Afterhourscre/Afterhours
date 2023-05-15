<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Helpdesk\Model\Ticket;

use Aheadworks\Helpdesk\Api\TicketRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Aheadworks\Helpdesk\Api\Data\TicketInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Finder
 *
 * @package Aheadworks\Helpdesk\Model\Ticket
 */
class Finder
{
    /**
     * @var TicketRepositoryInterface
     */
    private $ticketRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @param TicketRepositoryInterface $ticketRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        TicketRepositoryInterface $ticketRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->ticketRepository = $ticketRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * Retrieve tickets, assigned to the specific department
     *
     * @param $departmentId
     * @return TicketInterface[]
     */
    public function getByDepartmentId($departmentId)
    {
        $tickets = [];
        $this->searchCriteriaBuilder
            ->addFilter(TicketInterface::DEPARTMENT_ID, $departmentId);
        $searchCriteria = $this->searchCriteriaBuilder->create();
        try {
            $searchResult = $this->ticketRepository->getList($searchCriteria);
            $tickets = $searchResult->getItems();
        } catch (LocalizedException $exception) {
        }

        return $tickets;
    }
}
