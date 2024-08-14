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
 * @copyright   Copyright (c) Mageplaza (http://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\Barclaycard\Gateway\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Payment\Gateway\Config\Config;
use Mageplaza\Barclaycard\Model\Source\CardType;
use Mageplaza\Barclaycard\Model\Source\CardTypeMapper;
use Mageplaza\Barclaycard\Model\Source\PaymentAction;

/**
 * Class AbstractConfig
 * @package Mageplaza\Barclaycard\Gateway\Config
 */
class AbstractConfig extends Config
{
    /**
     * @var EncryptorInterface
     */
    protected $encryptor;

    /**
     * AbstractConfig constructor.
     *
     * @param ScopeConfigInterface $scopeConfig
     * @param EncryptorInterface $encryptor
     * @param null $methodCode
     * @param string $pathPattern
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        EncryptorInterface $encryptor,
        $methodCode = null,
        $pathPattern = self::DEFAULT_PATH_PATTERN
    ) {
        $this->encryptor = $encryptor;
        parent::__construct($scopeConfig, $methodCode, $pathPattern);
    }

    /**
     * @param null $storeId
     * @return mixed|null
     */
    public function getUserId($storeId = null)
    {
        return $this->getValue('user_id', $storeId);
    }

    /**
     * @param null $storeId
     * @return string
     */
    public function getShaIn($storeId = null)
    {
        return $this->encryptor->decrypt($this->getValue('sha_in', $storeId));
    }

    /**
     * @param null $storeId
     * @return false|string[]
     */
    public function getCcTypes($storeId = null)
    {
        return explode(',', $this->getValue('cctypes', $storeId));
    }

    /**
     * @param null $storeId
     * @return string
     */
    public function getExclCcTypes($storeId = null)
    {
        $exclType = array_diff(array_keys(CardType::getOptionArray()), $this->getCcTypes($storeId));

        $exclTypeMapper = [];

        foreach (CardTypeMapper::getOptionArray() as $typeMapper => $type) {
            if (in_array($type, $exclType, true)) {
                $exclTypeMapper[] = $typeMapper;
            }
        }

        return implode(';', $exclTypeMapper);
    }

    /**
     * @param null $storeId
     * @return mixed|null
     */
    public function getPaymentAction($storeId = null)
    {
        return $this->getValue('payment_action', $storeId);
    }

    /**
     * @param null $storeId
     * @return string
     */
    public function getPaymentActionMapper($storeId = null)
    {
        return $this->getPaymentAction($storeId) === PaymentAction::ACTION_AUTHORIZE ? 'PAU' : 'SAL';
    }
}
