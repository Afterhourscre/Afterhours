<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the mageplaza.com license that is
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

namespace Mageplaza\Worldpay\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Framework\HTTP\Adapter\CurlFactory;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\View\Asset\Repository;
use Magento\Sales\Model\Order;
use Magento\Store\Model\StoreManagerInterface;
use Mageplaza\Core\Helper\AbstractData;
use Mageplaza\Worldpay\Gateway\Config\Cards;
use Mageplaza\Worldpay\Model\Source\Environment;
use Zend_Serializer_Exception;

/**
 * Class Data
 * @package Mageplaza\Worldpay\Helper
 */
class Data extends AbstractData
{
    const CONFIG_MODULE_PATH  = 'mpworldpay';

    /**
     * @var string
     */
    protected $_serviceKey;

    /**
     * @var EncryptorInterface
     */
    protected $encryptor;

    /**
     * @var PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @var CurlFactory
     */
    protected $curlFactory;

    /**
     * @var Cards
     */
    protected $config;

    /**
     * @var Repository
     */
    protected $assetRepo;

    /**
     * Data constructor.
     *
     * @param Context $context
     * @param ObjectManagerInterface $objectManager
     * @param StoreManagerInterface $storeManager
     * @param EncryptorInterface $encryptor
     * @param PriceCurrencyInterface $priceCurrency
     * @param CurlFactory $curlFactory
     * @param Cards $config
     * @param Repository $assetRepo
     */
    public function __construct(
        Context $context,
        ObjectManagerInterface $objectManager,
        StoreManagerInterface $storeManager,
        EncryptorInterface $encryptor,
        PriceCurrencyInterface $priceCurrency,
        CurlFactory $curlFactory,
        Cards $config,
        Repository $assetRepo
    ) {
        $this->encryptor     = $encryptor;
        $this->priceCurrency = $priceCurrency;
        $this->curlFactory   = $curlFactory;
        $this->config        = $config;
        $this->assetRepo     = $assetRepo;

        parent::__construct($context, $objectManager, $storeManager);
    }

    /**
     * @param string $code
     * @param null $storeId
     *
     * @return mixed
     */
    public function getConfigGeneral($code = '', $storeId = null)
    {
        $value = parent::getConfigGeneral($code, $storeId);

        $obscureFields = ['service_key', 'client_key'];

        if (in_array($code, $obscureFields, true)) {
            return $this->encryptor->decrypt($value);
        }

        return $value;
    }

    /**
     * @param null $store
     *
     * @return string
     */
    public function getEnvironment($store = null)
    {
        return $this->getConfigGeneral('environment', $store);
    }

    /**
     * @param null $store
     *
     * @return string
     */
    public function getServiceKey($store = null)
    {
        return $this->_serviceKey ?: $this->getConfigGeneral('service_key', $store);
    }

    /**
     * @param string $serviceKey
     */
    public function setServiceKey($serviceKey)
    {
        $this->_serviceKey = $serviceKey;
    }

    /**
     * @param null $store
     *
     * @return string
     */
    public function getClientKey($store = null)
    {
        return $this->getConfigGeneral('client_key', $store);
    }

    /**
     * @param null $store
     *
     * @return string
     */
    public function getMerchantCountry($store = null)
    {
        return $this->getConfigGeneral('merchant_country', $store);
    }

    /**
     * @param string $currency
     * @param null $store
     *
     * @return string
     */
    public function getSettlementCurrency($currency, $store = null)
    {
        foreach ($this->getSiteCodes($store) as $rowIndex => $row) {
            if ($row['currency'] === $currency) {
                return $row['settlement'];
            }
        }

        return $this->getConfigGeneral('settlement_currency', $store);
    }

    /**
     * @param null $store
     *
     * @return string
     */
    public function getLanguageCode($store = null)
    {
        return $this->getConfigGeneral('language_code', $store) ?: 'EN';
    }

    /**
     * @param null $store
     *
     * @return bool
     */
    public function isDisplayLogo($store = null)
    {
        return (bool) $this->getConfigGeneral('display_logo', $store);
    }

    /**
     * @param null $stores
     *
     * @return array
     */
    public function getSiteCodes($stores = null)
    {
        try {
            return $this->unserialize($this->getConfigGeneral('site_codes', $stores));
        } catch (Zend_Serializer_Exception $e) {
            $this->_logger->critical($e->getMessage());

            return [];
        }
    }

    /**
     * @param string $currency
     * @param null $store
     *
     * @return string
     */
    public function getSiteCode($currency, $store = null)
    {
        foreach ($this->getSiteCodes($store) as $rowIndex => $row) {
            if ($row['currency'] === $currency) {
                return $row['site_code'];
            }
        }

        return null;
    }

    /**
     * @param null $store
     *
     * @return bool
     */
    public function isTestEnv($store = null)
    {
        return $this->getEnvironment($store) === Environment::SANDBOX;
    }

    /**
     * @return string
     */
    public function getUserAgent()
    {
        /** @var Http $request */
        $request = $this->_getRequest();

        return $request->getServer('HTTP_USER_AGENT');
    }

    /**
     * @return string
     */
    public function getIpAddress()
    {
        /** @var Http $request */
        $request = $this->_getRequest();
        $server  = $request->getServer();

        $ip = $server['REMOTE_ADDR'];
        if (!empty($server['HTTP_CLIENT_IP'])) {
            $ip = $server['HTTP_CLIENT_IP'];
        } elseif (!empty($server['HTTP_X_FORWARDED_FOR'])) {
            $ip = $server['HTTP_X_FORWARDED_FOR'];
        }
        $ipArr = explode(',', $ip);
        $ip    = $ipArr[count($ipArr) - 1];

        return trim($ip);
    }

    /**
     * @param float $amount
     * @param Order $order
     *
     * @return int
     */
    public function convertAmount($amount, $order)
    {
        $currency = $order->getOrderCurrencyCode();
        $scope    = $order->getStoreId();
        $max      = $order->getGrandTotal();

        if ($creditMemos = $order->getCreditmemosCollection()) {
            foreach ($creditMemos->getItems() as $item) {
                $max -= $item->getGrandTotal();
            }
        }

        $amount = $this->priceCurrency->convert($amount, $scope, $currency);

        return $this->formatAmount(min($amount, $max), $currency);
    }

    /**
     * @param float $amount
     * @param string|null $currency
     *
     * @return int
     */
    public function formatAmount($amount, $currency = null)
    {
        $multiplier = $currency === 'JPY' ? 1 : 100;

        return (int) ($amount * $multiplier);
    }

    /**
     * @param array $response
     * @param array|string $keys
     *
     * @return mixed|null
     */
    public function getInfo($response, $keys)
    {
        if (is_string($keys)) {
            return isset($response[$keys]) ? $response[$keys] : null;
        }

        if (is_array($keys)) {
            foreach ($keys as $key) {
                if (isset($response[$key])) {
                    if ($key === array_values(array_slice($keys, -1))[0]) {
                        return $response[$key];
                    }

                    array_shift($keys);

                    return $this->getInfo($response[$key], $keys);
                }
            }
        }

        return null;
    }

    /**
     * @param string $file
     *
     * @return string
     */
    public function getAssetUrl($file)
    {
        return $this->assetRepo->getUrl("Mageplaza_Worldpay::images/{$file}.png");
    }
}
