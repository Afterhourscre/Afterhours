<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Acr\Cron;

use Aheadworks\Acr\Model\Config;
use Aheadworks\Acr\Api\CartHistoryManagementInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;

/**
 * Class CartHistoryProcessor
 * @package Aheadworks\Acr\Cron
 */
class CartHistoryProcessor extends CronAbstract
{
    /**
     * Cron run interval in seconds
     */
    const RUN_INTERVAL = 270;

    /**
     * Cart history items to process per one cron run.
     */
    const ITEMS_PER_RUN = 100;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var CartHistoryManagementInterface
     */
    private $cartHistoryManagement;

    /**
     * @param DateTime $dateTime
     * @param Config $config
     * @param CartHistoryManagementInterface $cartHistoryManagement
     */
    public function __construct(
        DateTime $dateTime,
        Config $config,
        CartHistoryManagementInterface $cartHistoryManagement
    ) {
        $this->config = $config;
        $this->cartHistoryManagement = $cartHistoryManagement;
        parent::__construct($dateTime);
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        if (!$this->config->isEnabled()
            || $this->isLocked($this->config->getProcessCartHistoryLastExecTime(), self::RUN_INTERVAL)
        ) {
            return $this;
        }
        $this->cartHistoryManagement->processUnprocessedItems(self::ITEMS_PER_RUN);

        $this->config->setProcessCartHistoryLastExecTime($this->getCurrentTime());
        return $this;
    }
}
