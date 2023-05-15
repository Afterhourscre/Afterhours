<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Acr\Model\Quote;

use Aheadworks\Acr\Model\Config;
use Aheadworks\Acr\Api\CartHistoryManagementInterface;
use Magento\Quote\Model\Quote\Interceptor as QuoteInterceptor;
use Magento\Quote\Model\Quote;

class QuotePlugin
{
    /**
     * @var CartHistoryManagementInterface
     */
    private $cartHistoryManagement;

    /**
     * @var Config
     */
    private $config;

    /**
     * @param CartHistoryManagementInterface $cartHistoryManagement
     * @param Config $config
     */
    public function __construct(
        CartHistoryManagementInterface $cartHistoryManagement,
        Config $config
    ) {
        $this->cartHistoryManagement = $cartHistoryManagement;
        $this->config = $config;
    }

    /**
     * Add quote to cart history
     *
     * @param QuoteInterceptor $interceptor
     * @param Quote $quote
     * @return Quote
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterAfterSave(
        QuoteInterceptor $interceptor,
        Quote $quote
    ) {
        if ($this->config->isEnabled()
            && $quote->getCustomerEmail()
        ) {
            $cartData = array_merge($quote->getData(), [
                'email' => $quote->getCustomerEmail(),
                'customer_name' => $quote->getCustomerFirstname() . ' ' . $quote->getCustomerLastname()
            ]);
            $this->cartHistoryManagement->addCartToCartHistory($cartData);
        }
        return $quote;
    }
}
