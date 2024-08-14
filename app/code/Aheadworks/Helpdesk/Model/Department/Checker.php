<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Helpdesk\Model\Department;

use Aheadworks\Helpdesk\Model\Ticket\Finder as TicketFinder;

/**
 * Class Checker
 *
 * @package Aheadworks\Helpdesk\Model\Department
 */
class Checker
{
    /**
     * @var TicketFinder
     */
    private $ticketFinder;

    /**
     * @param TicketFinder $ticketFinder
     */
    public function __construct(
        TicketFinder $ticketFinder
    ) {
        $this->ticketFinder = $ticketFinder;
    }

    /**
     * Check if department has tickets assigned to it
     *
     * @param $departmentId
     * @return bool
     */
    public function hasTicketsAssigned($departmentId)
    {
        $tickets = $this->ticketFinder->getByDepartmentId($departmentId);
        return is_array($tickets) && (count($tickets) > 0);
    }
}
