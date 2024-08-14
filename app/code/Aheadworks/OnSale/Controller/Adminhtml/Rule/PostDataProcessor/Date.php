<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Controller\Adminhtml\Rule\PostDataProcessor;

use Aheadworks\OnSale\Controller\Adminhtml\Label\PostDataProcessor\ProcessorInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\Stdlib\DateTime as StdlibDateTime;
use Aheadworks\OnSale\Api\Data\RuleInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

/**
 * Class Date
 *
 * @package Aheadworks\OnSale\Controller\Adminhtml\Rule\PostDataProcessors
 */
class Date implements ProcessorInterface
{
    /**
     * @var DateTime
     */
    private $dateTime;

    /**
     * @var TimezoneInterface
     */
    private $timezone;

    /**
     * @param DateTime $dateTime
     * @param TimezoneInterface $timezone
     */
    public function __construct(
        DateTime $dateTime,
        TimezoneInterface $timezone
    ) {
        $this->dateTime = $dateTime;
        $this->timezone = $timezone;
    }

    /**
     * Prepare dates for save
     *
     * @param array $data
     * @return array
     */
    public function process($data)
    {
        empty($data[RuleInterface::FROM_DATE])
            ? $data[RuleInterface::FROM_DATE] = null
            : $data[RuleInterface::FROM_DATE] = $this->convertDate($data[RuleInterface::FROM_DATE]);
        empty($data[RuleInterface::TO_DATE])
            ? $data[RuleInterface::TO_DATE] = null
            : $data[RuleInterface::TO_DATE] = $this->convertDate($data[RuleInterface::TO_DATE]);
        return $data;
    }

    /**
     * Convert date
     *
     * @param string $dateFromForm
     * @return string
     */
    private function convertDate($dateFromForm)
    {
        $dateByTimezone = $this->timezone
            ->date($dateFromForm, null, true, false)
            ->format('Y-m-d');

        return $this->dateTime->gmtDate(
            StdlibDateTime::DATE_PHP_FORMAT,
            strtotime($dateByTimezone)
        );
    }
}
