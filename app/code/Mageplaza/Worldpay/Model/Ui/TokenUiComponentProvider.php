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

namespace Mageplaza\Worldpay\Model\Ui;

use Magento\Vault\Api\Data\PaymentTokenInterface;
use Magento\Vault\Model\Ui\TokenUiComponentInterface;
use Magento\Vault\Model\Ui\TokenUiComponentInterfaceFactory;
use Magento\Vault\Model\Ui\TokenUiComponentProviderInterface as TokenUiInterface;
use Mageplaza\Worldpay\Helper\Data;
use Mageplaza\Worldpay\Model\Payment\Cards;

/**
 * Class TokenUiComponentProvider
 * @package Mageplaza\Worldpay\Model\Ui
 */
class TokenUiComponentProvider implements TokenUiInterface
{
    /**
     * @var TokenUiComponentInterfaceFactory
     */
    private $componentFactory;

    /**
     * TokenUiComponentProvider constructor.
     *
     * @param TokenUiComponentInterfaceFactory $componentFactory
     */
    public function __construct(TokenUiComponentInterfaceFactory $componentFactory)
    {
        $this->componentFactory = $componentFactory;
    }

    /**
     * @param PaymentTokenInterface $paymentToken
     *
     * @return TokenUiComponentInterface
     */
    public function getComponentForToken(PaymentTokenInterface $paymentToken)
    {
        $jsonDetails = Data::jsonDecode($paymentToken->getTokenDetails());

        $component = $this->componentFactory->create([
            'name'   => 'Mageplaza_Worldpay/js/view/payment/method-renderer/vault',
            'config' => [
                'code'                                  => Cards::VAULT,
                'token'                                 => $paymentToken->getGatewayToken(),
                'methodCode'                            => Cards::CODE,
                TokenUiInterface::COMPONENT_DETAILS     => $jsonDetails,
                TokenUiInterface::COMPONENT_PUBLIC_HASH => $paymentToken->getPublicHash(),
            ]
        ]);

        return $component;
    }
}
