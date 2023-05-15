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

use Magento\Framework\DataObject;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\UrlInterface;
use Magento\Payment\Gateway\Command\CommandManagerInterface;
use Magento\Payment\Gateway\Command\CommandPoolInterface;
use Magento\Payment\Gateway\Config\ValueHandlerPoolInterface;
use Magento\Payment\Gateway\Data\PaymentDataObjectFactory;
use Magento\Payment\Gateway\Validator\ValidatorPoolInterface;
use Magento\Payment\Model\Method\Adapter;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Api\Data\PaymentInterface;
use Mageplaza\Worldpay\Helper\Data;

/**
 * Class AbstractPayment
 * @package Mageplaza\Worldpay\Model
 */
class AbstractPayment extends Adapter
{
    const CODE = '';

    const COUNTRY = [];

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * AbstractPayment constructor.
     *
     * @param ManagerInterface $eventManager
     * @param ValueHandlerPoolInterface $valueHandlerPool
     * @param PaymentDataObjectFactory $paymentDataObjectFactory
     * @param string $code
     * @param string $formBlockType
     * @param string $infoBlockType
     * @param UrlInterface $urlBuilder
     * @param Data $helper
     * @param CommandPoolInterface|null $commandPool
     * @param ValidatorPoolInterface|null $validatorPool
     * @param CommandManagerInterface|null $commandExecutor
     */
    public function __construct(
        ManagerInterface $eventManager,
        ValueHandlerPoolInterface $valueHandlerPool,
        PaymentDataObjectFactory $paymentDataObjectFactory,
        $code,
        $formBlockType,
        $infoBlockType,
        UrlInterface $urlBuilder,
        Data $helper,
        CommandPoolInterface $commandPool = null,
        ValidatorPoolInterface $validatorPool = null,
        CommandManagerInterface $commandExecutor = null
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->helper     = $helper;

        parent::__construct(
            $eventManager,
            $valueHandlerPool,
            $paymentDataObjectFactory,
            $code,
            $formBlockType,
            $infoBlockType,
            $commandPool,
            $validatorPool,
            $commandExecutor
        );
    }

    /**
     * @param CartInterface|null $quote
     *
     * @return bool|mixed
     */
    public function isAvailable(CartInterface $quote = null)
    {
        $country = static::COUNTRY;

        return parent::isAvailable($quote) && (!$country || isset($country[$this->helper->getMerchantCountry()]));
    }

    /**
     * @return string
     * @throws LocalizedException
     */
    public function getTitle()
    {
        if (!$instance = $this->getInfoInstance()) {
            return parent::getTitle();
        }

        $data = $instance->getAdditionalInformation();

        return isset($data['method_title']) ? $data['method_title'] : parent::getTitle();
    }

    /**
     * Assign data to info model instance
     *
     * @param array|DataObject $data
     *
     * @return $this
     * @throws LocalizedException
     */
    public function assignData(DataObject $data)
    {
        parent::assignData($data);

        $additionalData = $data->getData(PaymentInterface::KEY_ADDITIONAL_DATA);

        if (!is_array($additionalData)) {
            return $this;
        }

        foreach ($this->getPaymentInfoKeys() as $key) {
            if (isset($additionalData[$key])) {
                $this->getInfoInstance()->setAdditionalInformation($key, $additionalData[$key]);
            }
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getPaymentInfoKeys()
    {
        return explode(',', $this->getConfigData('paymentInfoKeys'));
    }

    /**
     * Retrieve assoc array of checkout configuration
     *
     * @return array
     */
    public function getConfig()
    {
        $logo = static::CODE === 'mpworldpay_cards' ? 'worldpay' : str_replace('mpworldpay_', '', static::CODE);

        return [
            'isLogo'          => $this->helper->isDisplayLogo(),
            'logo'            => $this->helper->getAssetUrl($logo),
            'clientKey'       => $this->helper->getClientKey(),
            'languageCode'    => $this->helper->getLanguageCode(),
            'merchantCountry' => $this->helper->getMerchantCountry(),
        ];
    }
}
