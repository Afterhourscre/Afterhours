<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Coupongenerator\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Store\Model\Store;
use Magento\SalesRule\Api\Data\RuleInterface;

/**
 * Class Discount
 * @package Aheadworks\Coupongenerator\Ui\Component\Listing\Column
 * @codeCoverageIgnore
 */
class Discount extends \Magento\Ui\Component\Listing\Columns\Column
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Magento\Framework\Locale\CurrencyInterface
     */
    private $localeCurrency;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param \Magento\Framework\Locale\CurrencyInterface $localeCurrency
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        \Magento\Framework\Locale\CurrencyInterface $localeCurrency,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->localeCurrency = $localeCurrency;
        $this->storeManager = $storeManager;
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            /** @var Store $store */
            $store = $this->storeManager->getStore(Store::DEFAULT_STORE_ID);
            $currencyCode = $store->getCurrentCurrencyCode();
            $currencyRate = $store->getCurrentCurrencyRate();
            $currency = $this->localeCurrency->getCurrency($currencyCode);

            $fieldName = $this->getData('name');
            foreach ($dataSource['data']['items'] as & $item) {
                if (isset($item[$fieldName]) && isset($item['simple_action'])) {
                    switch ($item['simple_action']) {
                        case RuleInterface::DISCOUNT_ACTION_BY_PERCENT:
                            $item[$fieldName] = (number_format($item[$fieldName], 2)+0).'%';
                            break;
                        case RuleInterface::DISCOUNT_ACTION_FIXED_AMOUNT:
                        case RuleInterface::DISCOUNT_ACTION_FIXED_AMOUNT_FOR_CART:
                            $item[$fieldName] = $currency->toCurrency(sprintf("%f", $item[$fieldName] * $currencyRate));
                            break;
                        case RuleInterface::DISCOUNT_ACTION_BUY_X_GET_Y:
                            $item[$fieldName] = __(
                                "Buy %1 Get %2",
                                [$item['discount_step'], (number_format($item['discount_amount'], 2)+0)]
                            );
                            break;
                    }
                }
            }
        }

        return $dataSource;
    }
}
