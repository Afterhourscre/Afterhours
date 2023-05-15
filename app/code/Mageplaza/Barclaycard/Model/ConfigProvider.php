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
 * @package     Mageplaza_Barclaycard
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\Barclaycard\Model;

use Magento\Checkout\Model\ConfigProviderInterface;
use Mageplaza\Barclaycard\Model\Payment\AbstractPayment;

/**
 * Class ConfigProvider
 */
class ConfigProvider implements ConfigProviderInterface
{
    /**
     * @var AbstractPayment[]
     */
    private $paymentProviders;

    /**
     * ConfigProvider constructor.
     *
     * @param array $paymentProviders
     */
    public function __construct($paymentProviders)
    {
        $this->paymentProviders = $paymentProviders;
    }

    /**
     * Retrieve assoc array of checkout configuration
     *
     * @return array
     */
    public function getConfig()
    {
        $payments = [];

        foreach ($this->paymentProviders as $payment) {
            $payments[$payment->getCode()] = $payment->getConfig();
        }

        return ['payment' => $payments];
    }
}