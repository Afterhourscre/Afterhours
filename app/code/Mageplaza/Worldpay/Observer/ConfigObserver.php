<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_Worldpay
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\Worldpay\Observer;

use Magento\Config\Model\ResourceModel\Config as ModelConfig;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Mageplaza\Worldpay\Gateway\Config\Cards;

/**
 * Class ConfigObserver
 * @package Mageplaza\Worldpay\Observer
 */
class ConfigObserver implements ObserverInterface
{
    /**
     * @var ModelConfig
     */
    private $modelConfig;

    /**
     * @var Cards
     */
    private $config;

    /**
     * ConfigObserver constructor.
     *
     * @param ModelConfig $modelConfig
     * @param Cards $config
     */
    public function __construct(
        ModelConfig $modelConfig,
        Cards $config
    ) {
        $this->modelConfig = $modelConfig;
        $this->config = $config;
    }

    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $this->modelConfig->saveConfig(
            'payment/mpworldpay_cards_vault/order_status',
            $this->config->getOrderStatus(),
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
            0
        );
    }
}
