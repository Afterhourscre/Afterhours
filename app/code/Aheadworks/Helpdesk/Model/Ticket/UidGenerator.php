<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Helpdesk\Model\Ticket;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Math\Random;
use Aheadworks\Helpdesk\Api\TicketRepositoryInterface;

/**
 * Class UidGenerator
 *
 * @package Aheadworks\Helpdesk\Model\Ticket
 */
class UidGenerator
{
    /**
     * Substrings which are forbidden for usage
     */
    const FORBIDDEN_SUBSTRINGS = 'sex,wtf,fuc,fuk,fck,ass,hui,dck,pzd,ebl,bla,xep,lol,gay,omg';

    /**
     * Length of alphabetical prefix
     */
    const ALPHABETICAL_PREFIX_LENGTH = 3;

    /**
     * Separator between prefix and postfix
     */
    const SEPARATOR = '-';

    /**
     * Length of numeric postfix
     */
    const NUMERIC_POSTFIX_LENGTH = 5;

    /**
     * @var Random
     */
    private $random;

    /**
     * @var TicketRepositoryInterface
     */
    private $ticketRepository;

    /**
     * @param Random $random
     * @param TicketRepositoryInterface $ticketRepository
     */
    public function __construct(
        Random $random,
        TicketRepositoryInterface $ticketRepository
    ) {
        $this->random = $random;
        $this->ticketRepository = $ticketRepository;
    }

    /**
     * Generate unique alphanumeric ID for ticket
     *
     * @return string
     */
    public function getUid()
    {
        do {
            try {
                $uid = $this->getAlphabeticalPrefix() . self::SEPARATOR . $this->getNumericPostfix();
            } catch (LocalizedException $exception) {
                continue;
            }

            try {
                $ticketWithTheSameUid = $this->ticketRepository->getByUid($uid);
            } catch (LocalizedException $exception) {
                $ticketWithTheSameUid = null;
            }
        } while (stripos(self::FORBIDDEN_SUBSTRINGS, $uid) !== false || $ticketWithTheSameUid !== null);

        return $uid;
    }

    /**
     * Retrieve random generated alphabetical prefix
     *
     * @return string
     * @throws LocalizedException
     */
    private function getAlphabeticalPrefix()
    {
        return $this->random->getRandomString(
            self::ALPHABETICAL_PREFIX_LENGTH,
            Random::CHARS_UPPERS
        );
    }

    /**
     * Retrieve random generated numeric postfix
     *
     * @return string
     * @throws LocalizedException
     */
    private function getNumericPostfix()
    {
        $randomNumber = $this->random->getRandomNumber(
            0,
            pow(10, self::NUMERIC_POSTFIX_LENGTH) - 1
        );
        $numericPostfix = sprintf(
            '%0'. self::NUMERIC_POSTFIX_LENGTH . 'd',
            $randomNumber
        );
        return $numericPostfix;
    }
}
