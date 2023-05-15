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

namespace Mageplaza\Worldpay\Model\Ui\Adminhtml;

use Magento\Framework\View\Element\Template;
use Magento\Vault\Api\Data\PaymentTokenInterface;
use Magento\Vault\Model\Ui\TokenUiComponentInterface;
use Magento\Vault\Model\Ui\TokenUiComponentInterfaceFactory;
use Magento\Vault\Model\Ui\TokenUiComponentProviderInterface as TokenUiInterface;
use Mageplaza\Worldpay\Helper\Data;
use Mageplaza\Worldpay\Model\Payment\Cards;

/**
 * Class TokenUiComponentProvider
 * @package Mageplaza\Worldpay\Model\Ui\Adminhtml
 */
class TokenUiComponentProvider implements TokenUiInterface
{
    /**
     * @var TokenUiComponentInterfaceFactory
     */
    private $componentFactory;

    /**
     * @var Data
     */
    private $helper;

    /**
     * @param TokenUiComponentInterfaceFactory $componentFactory
     * @param Data $helper
     */
    public function __construct(
        TokenUiComponentInterfaceFactory $componentFactory,
        Data $helper
    ) {
        $this->componentFactory = $componentFactory;
        $this->helper           = $helper;
    }

    /**
     * @param PaymentTokenInterface $paymentToken
     *
     * @return TokenUiComponentInterface
     */
    public function getComponentForToken(PaymentTokenInterface $paymentToken)
    {
        $jsonDetails = Data::jsonDecode($paymentToken->getTokenDetails());
        $clientKey   = Data::jsonEncode(['clientKey' => $this->helper->getClientKey()]);

        $component = $this->componentFactory->create([
            'name'   => Template::class,
            'config' => [
                'clientKey'                             => $clientKey,
                'code'                                  => Cards::VAULT,
                'token'                                 => $paymentToken->getGatewayToken(),
                'template'                              => 'Mageplaza_Worldpay::form/vault.phtml',
                TokenUiInterface::COMPONENT_DETAILS     => $jsonDetails,
                TokenUiInterface::COMPONENT_PUBLIC_HASH => $paymentToken->getPublicHash(),

            ],
        ]);

        return $component;
    }
}
