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
 * @package     Mageplaza_Barclaycard
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\Barclaycard\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\HTTP\Adapter\CurlFactory;
use Magento\Framework\HTTP\PhpEnvironment\Request as EnvRequest;
use Magento\Framework\Locale\Resolver;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\UrlInterface;
use Magento\Payment\Gateway\Helper\ContextHelper;
use Magento\Payment\Gateway\Helper\SubjectReader;
use Magento\Payment\Model\InfoInterface;
use Magento\Quote\Model\Quote\Payment;
use Magento\Sales\Model\Order;
use Magento\Store\Model\StoreManagerInterface;
use Mageplaza\Barclaycard\Gateway\Config\Hosted;
use Mageplaza\Barclaycard\Model\Source\ECI;
use Mageplaza\Barclaycard\Model\Source\PaymentInfo;
use Mageplaza\Core\Helper\AbstractData;

/**
 * Class Data
 * @package Mageplaza\Barclaycard\Helper
 */
class Data extends AbstractData
{
    const CONFIG_MODULE_PATH = 'mpbarclaycard';

    const UPLOAD_DIR = 'mageplaza/barclaycard/';

    /**
     * @var PriceCurrencyInterface
     */
    private $priceCurrency;

    /**
     * @var EnvRequest
     */
    private $envRequest;

    /**
     * @var CurlFactory
     */
    protected $curlFactory;

    /**
     * @var Resolver
     */
    private $resolver;

    /**
     * @var Hosted
     */
    protected $config;

    /**
     * Data constructor.
     *
     * @param Context $context
     * @param ObjectManagerInterface $objectManager
     * @param StoreManagerInterface $storeManager
     * @param PriceCurrencyInterface $priceCurrency
     * @param EnvRequest $envRequest
     * @param CurlFactory $curlFactory
     * @param Resolver $resolver
     * @param Hosted $config
     */
    public function __construct(
        Context $context,
        ObjectManagerInterface $objectManager,
        StoreManagerInterface $storeManager,
        PriceCurrencyInterface $priceCurrency,
        EnvRequest $envRequest,
        CurlFactory $curlFactory,
        Resolver $resolver,
        Hosted $config
    ) {
        $this->priceCurrency = $priceCurrency;
        $this->envRequest    = $envRequest;
        $this->curlFactory   = $curlFactory;
        $this->resolver      = $resolver;
        $this->config        = $config;

        parent::__construct($context, $objectManager, $storeManager);
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
    public function getPspId($store = null)
    {
        return $this->getConfigGeneral('psp_id', $store);
    }

    /**
     * @param null $store
     *
     * @return string
     */
    public function getHashAlgorithm($store = null)
    {
        return $this->getConfigGeneral('hash_algorithm', $store);
    }

    /**
     * @param float $amount
     * @param null $scope
     * @param null $currency
     *
     * @return float
     */
    public function convertPrice($amount, $scope = null, $currency = null)
    {
        return $this->priceCurrency->convert($amount, $scope, $currency);
    }

    /**
     * @param float $amount
     * @param Order $order
     *
     * @return float
     */
    public function convertAmount($amount, $order)
    {
        $amount = $this->convertPrice($amount, $order->getStoreId(), $order->getOrderCurrencyCode());

        return number_format($amount, 2, '.', '') * 100;
    }

    /**
     * @param array $response
     * @param array|string $keys
     *
     * @return mixed
     */
    public function getInfo($response, $keys)
    {
        if (is_string($keys)) {
            return $response[$keys] ?? null;
        }

        if (!is_array($keys)) {
            return null;
        }

        foreach ($keys as $key) {
            if (!isset($response[$key])) {
                continue;
            }

            if ($key === array_values(array_slice($keys, -1))[0]) {
                return $response[$key];
            }

            array_shift($keys);

            return $this->getInfo($response[$key], $keys);
        }

        return null;
    }

    /**
     * @param Payment|Order\Payment $payment
     * @param bool $isFrontend
     *
     * @return array
     * @throws LocalizedException
     */
    public function getCardInfo($payment, $isFrontend = true)
    {
        $ccNumber = preg_replace('/\D/', '', $payment->getAdditionalInformation('cc_number'));
        $expMonth = sprintf('%02d', $payment->getAdditionalInformation('cc_exp_month'));
        $expYear  = substr($payment->getAdditionalInformation('cc_exp_year'), 2);
        $cvc      = $payment->getAdditionalInformation('cc_cid');

        $info[PaymentInfo::CC_LAST_4] = substr($ccNumber, -4);

        if ($expMonth && $expYear) {
            $info[PaymentInfo::CC_EXP_DATE] = $expMonth . $expYear;
        }

        $this->recollectInformation($payment, $info);

        return [
            'CARDNO' => $ccNumber,
            'ED'     => $expMonth . $expYear,
            'CVC'    => $cvc,
            'ECI'    => $isFrontend ? ECI::ECOM : ECI::MOTO,
        ];
    }

    /**
     * @param Payment|Order\Payment $payment
     * @param array $info
     *
     * @throws LocalizedException
     */
    public function recollectInformation($payment, $info)
    {
        $payment->unsAdditionalInformation();

        foreach ($info as $key => $value) {
            if ($value) {
                $payment->setAdditionalInformation($key, $value);
            }
        }
    }

    /**
     * @param array $buildSubject
     *
     * @return InfoInterface|Order\Payment
     */
    public function getValidPaymentInstance($buildSubject)
    {
        $paymentDataObject = SubjectReader::readPayment($buildSubject);

        $payment = $paymentDataObject->getPayment();

        ContextHelper::assertOrderPayment($payment);

        return $payment;
    }

    /**
     * @return string
     */
    public function getRemoteIpAddress()
    {
        return $this->envRequest->getServer('REMOTE_ADDR');
    }

    /**
     * @return string|null
     */
    public function getLocaleCode()
    {
        return $this->resolver->getLocale();
    }

    /**
     * @return string
     */
    public function getUserAgent()
    {
        /** @var Http $request */
        $request = $this->_getRequest();

        return str_replace(' ', '', $request->getServer('HTTP_USER_AGENT'));
    }

    /**
     * @param string $file
     *
     * @return string
     */
    public function getAssetUrl($file)
    {
        if (!$file) {
            return '';
        }

        try {
            $store = $this->storeManager->getStore();
        } catch (NoSuchEntityException $e) {
            $this->_logger->critical($e);

            return '';
        }

        return $store->getBaseUrl(UrlInterface::URL_TYPE_MEDIA) . self::UPLOAD_DIR . $file;
    }

    /**
     * @param array $body
     * @param string $shaIn
     * @param null $hashAlgorithm
     */
    public function appendShaSign(&$body, $shaIn, $hashAlgorithm = null)
    {
        if (!$hashAlgorithm) {
            $hashAlgorithm = $this->getHashAlgorithm();
        }

        unset($body['SHASIGN']);

        $body = array_change_key_case($body, CASE_UPPER);

        ksort($body);

        $shaSign = '';
        foreach ($body as $key => $value) {
            if ($value !== '') {
                $shaSign .= $key . '=' . $value . $shaIn;
            }
        }

        $body['SHASIGN'] = strtoupper(hash($hashAlgorithm, $shaSign));
    }

    /**
     * @param array $data
     *
     * @return array
     */
    public function prepareBody($data)
    {
        $body = [];

        foreach ($data as $key => $value) {
            if ($value || $value === 0) {
                $body[$key] = $value;
            }
        }

        return $body;
    }
}
