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

namespace Mageplaza\Worldpay\Model\Payment;

use Mageplaza\Worldpay\Model\Source\CardTypeMapper;
use Mageplaza\Worldpay\Model\Source\DisplayCheckout;

/**
 * Class Cards
 * @package Mageplaza\Worldpay\Model\Payment
 */
class Cards extends AbstractPayment
{
    const CODE  = 'mpworldpay_cards';
    const VAULT = 'mpworldpay_cards_vault';

    /**
     * @return array
     */
    public function getConfig()
    {
        return array_merge(parent::getConfig(), [
            'ccTypes'     => CardTypeMapper::getAllCardTypes(explode(',', $this->getConfigData('cctypes'))),
            'ccVaultCode' => self::VAULT,
            'use3ds'      => (bool) $this->getConfigData('use3ds'),
            'isIframe'    => $this->getConfigData('display_checkout') === DisplayCheckout::IFRAME,
            'secureUrl'   => $this->urlBuilder->getUrl('mpworldpay/index/process3ds', ['_secure' => true]),
        ]);
    }
}
