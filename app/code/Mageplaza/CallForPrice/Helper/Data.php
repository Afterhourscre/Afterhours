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
 * @package     Mageplaza_CallForPrice
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\CallForPrice\Helper;

use Magento\Customer\Model\Context as CustomerContext;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Area;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\View\Asset\Repository as AssetFile;
use Magento\Store\Model\StoreManagerInterface;
use Mageplaza\Core\Helper\AbstractData;

/**
 * Class Data
 * @package Mageplaza\CallForPrice\Helper
 */
class Data extends AbstractData
{
    const CONFIG_MODULE_PATH = 'mpcallforprice';
    const XML_PATH_EMAIL     = 'email';

    /**
     * @var CustomerSession
     */
    protected $_customerSession;

    /**
     * @var HttpContext
     */
    protected $_httpContext;

    /**
     * @var AssetFile
     */
    protected $_assetRepo;

    /**
     * @var Http
     */
    protected $_requestHttp;

    /**
     * @var TransportBuilder
     */
    protected $transportBuilder;

    /**
     * Data constructor.
     *
     * @param Context $context
     * @param ObjectManagerInterface $objectManager
     * @param StoreManagerInterface $storeManager
     * @param CustomerSession $customerSession
     * @param HttpContext $httpContext
     * @param AssetFile $assetRepo
     * @param Http $requestHttp
     * @param TransportBuilder $transportBuilder
     */
    public function __construct(
        Context $context,
        ObjectManagerInterface $objectManager,
        StoreManagerInterface $storeManager,
        CustomerSession $customerSession,
        HttpContext $httpContext,
        AssetFile $assetRepo,
        Http $requestHttp,
        TransportBuilder $transportBuilder
    )
    {
        $this->_customerSession = $customerSession;
        $this->_httpContext     = $httpContext;
        $this->_assetRepo       = $assetRepo;
        $this->_requestHttp     = $requestHttp;
        $this->transportBuilder = $transportBuilder;

        parent::__construct($context, $objectManager, $storeManager);
    }

    /**
     * @return bool
     */
    public function getCustomerLogedIn()
    {
        return $this->_httpContext->getValue(CustomerContext::CONTEXT_AUTH);
    }

    /**
     * @return int
     */
    public function getStoreId()
    {
        return $this->storeManager->getStore()->getId();
    }

    /**
     * @return mixed|null
     */
    public function getCustomerGroupId()
    {
        return $this->_httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_GROUP);
    }

    /**
     * @return mixed
     * @throws \Zend_Serializer_Exception
     */
    public function getRequestStatusConfig()
    {
        return $this->unserialize($this->getConfigGeneral('request_status'));
    }

    /**
     * @param null $storeId
     *
     * @return mixed
     */
    public function getDisableCompareWishlistConfig($storeId = null)
    {
        return $this->getModuleConfig('disable_default_function/disable_compare_wishlist', $storeId);
    }

    /**
     * @param null $storeId
     *
     * @return mixed
     */
    public function getDisableRegisterCustomerConfig($storeId = null)
    {
        return $this->getModuleConfig('disable_default_function/disable_register_customer', $storeId);
    }

    /**
     * @param null $storeId
     *
     * @return mixed
     */
    public function getDisableCartByGroupConfig($storeId = null)
    {
        return array_filter(explode(',', $this->getModuleConfig('disable_default_function/disable_cart_by_group', $storeId)), 'strlen');
    }

    /**
     * @param null $storeId
     *
     * @return mixed
     */
    public function getDisableCheckoutByGroupConfig($storeId = null)
    {
        return array_filter(explode(',', $this->getModuleConfig('disable_default_function/disable_checkout_by_group', $storeId)), 'strlen');
    }

    /**
     * @param null $storeId
     *
     * @return mixed
     */
    public function getTACDefaultCheckedConfig($storeId = null)
    {
        return $this->getModuleConfig('terms_conditions/checked_by_default', $storeId);
    }

    /**
     * @param null $storeId
     *
     * @return mixed
     */

    public function getTACRequiredConfig($storeId = null)
    {
        return $this->getModuleConfig('terms_conditions/terms_required', $storeId);
    }

    /**
     * @param null $storeId
     * @return mixed
     */
    public function getTACLabel($storeId = null)
    {
        $title = $this->getModuleConfig('terms_conditions/title', $storeId);
        $text  = $this->getModuleConfig('terms_conditions/anchor', $storeId);
        if (!$text) {
            return $title;
        }

        $url = $this->getModuleConfig('terms_conditions/url', $storeId);
        if (filter_var($url, FILTER_VALIDATE_URL)) {
            $text = '<a href=\"' . $url . '\" title=\"' . $text . '\" target=\"_blank\"><span>' . $text . '</span></a>';
        }

        return str_replace('%anchor', $text, $title);
    }

    /**
     * @param null $storeId
     *
     * @return mixed
     */
    public function getEmailEnableConfig($storeId = null)
    {
        return $this->getModuleConfig('admin_email/enabled', $storeId);
    }

    /**
     * @param null $storeId
     *
     * @return mixed
     */
    public function getEmailSenderConfig($storeId = null)
    {
        return $this->getModuleConfig('admin_email/sender', $storeId);
    }

    /**
     * @param null $storeId
     *
     * @return mixed
     */
    public function getEmailSendtoConfig($storeId = null)
    {
        return $this->getModuleConfig('admin_email/sendto', $storeId);
    }

    /**
     * @param null $storeId
     *
     * @return mixed
     */
    public function getRequestQuoteUrl($storeId = null)
    {
        return $this->_getUrl('callforprice/index/requestquote', $storeId);
    }

    /**
     * @return string
     */
    public function getLoginUrl()
    {
        return $this->_getUrl('customer/account/login');
    }

    /**
     * @return string
     */
    public function getFullActionName()
    {
        return $this->_requestHttp->getFullActionName();
    }

    /**
     * @param $sendTo
     * @param $name
     * @param $email
     * @param $phone
     * @param $note
     * @param $productName
     * @param $productUrl
     * @param $emailTemplate
     * @param $storeId
     * @param $productImageUrl
     * @return bool
     */
    public function sendMail($sendTo, $name, $email, $phone, $note, $productName, $productUrl, $emailTemplate, $storeId, $productImageUrl)
    {
        try {
            $this->transportBuilder
                ->setTemplateIdentifier($emailTemplate)
                ->setTemplateOptions([
                    'area'  => Area::AREA_FRONTEND,
                    'store' => $storeId,
                ])
                ->setTemplateVars([
                    'name'            => $name,
                    'email'           => $email,
                    'phone'           => $phone,
                    'note'            => $note,
                    'productName'     => $productName,
                    'productUrl'      => $productUrl,
                    'productImageUrl' => $productImageUrl,
                ])
                ->setFrom($this->getEmailSenderConfig($storeId))
                ->addTo($sendTo);
            $transport = $this->transportBuilder->getTransport();
            $transport->sendMessage();

            return true;
        } catch (\Magento\Framework\Exception\MailException $e) {
            $this->_logger->critical($e->getLogMessage());
        }

        return false;
    }

    /**
     * @return array
     */
    public function getDateRange()
    {
        if ($dateRange = $this->_request->getParam('dateRange')) {
            $startDate = $dateRange[0];
            $endDate   = $dateRange[1];
            list($startDate, $endDate) = $this->getDateTimeRangeFormat($startDate, $endDate);
            $compareStartDate = null;
            $compareEndDate   = null;
            if (isset($dateRange[2]) && $dateRange[2] != '' && isset($dateRange[3]) && $dateRange[3] != '') {
                $compareStartDate = $dateRange[2];
                $compareEndDate   = $dateRange[3];
                list($compareStartDate, $compareEndDate) = $this->getDateTimeRangeFormat($compareStartDate, $compareEndDate);
            }
        } else {
            list($startDate, $endDate) = $this->getDateTimeRangeFormat('-1 month', 'now');
            $days = date('z', strtotime($endDate) - strtotime($startDate));
            list($compareStartDate, $compareEndDate) = $this->getDateTimeRangeFormat('-1 month -' . ($days + 1) . ' day', '-1 month -1 day');
        }

        return [$startDate, $endDate, $compareStartDate, $compareEndDate];
    }

    /**
     * @param      $startDate
     * @param null $endDate
     * @param null $isConvertToLocalTime
     *
     * @return array
     */
    public function getDateTimeRangeFormat($startDate, $endDate = null, $isConvertToLocalTime = null)
    {
        if (!$endDate) {
            $endDate = $startDate;
        }
        $startDate = (new \DateTime($startDate, new \DateTimeZone($this->getTimezone())))->setTime(0, 0, 0);
        $endDate   = (new \DateTime($endDate, new \DateTimeZone($this->getTimezone())))->setTime(23, 59, 59);

        if ($isConvertToLocalTime) {
            $startDate->setTimezone(new \DateTimeZone('UTC'));
            $endDate->setTimezone(new \DateTimeZone('UTC'));
        }

        return [$startDate->format('Y-m-d H:i:s'), $endDate->format('Y-m-d H:i:s')];
    }

    /**
     * @return array|mixed
     */
    public function getTimezone()
    {
        return $this->getConfigValue('general/locale/timezone');
    }
}
