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

namespace Mageplaza\CallForPrice\Controller\Index;

use Magento\Catalog\Helper\ImageFactory;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Stdlib\DateTime;
use Magento\Framework\View\Result\PageFactory;
use Mageplaza\CallForPrice\Helper\Data as HelperData;
use Mageplaza\CallForPrice\Helper\Rule as HelperRule;
use Mageplaza\CallForPrice\Model\RequestsFactory;
use Mageplaza\CallForPrice\Model\RequestState;

/**
 * Class Requestquote
 * @package Mageplaza\CallForPrice\Controller\Index
 */
class Requestquote extends Action
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var HelperData
     */
    protected $_helperData;

    /**
     * @var HelperRule
     */
    protected $_helperRule;

    /**
     * @var JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var HelperRule
     */
    protected $requestsFactory;

    /**
     * @var ImageFactory
     */
    protected $imageFactory;

    /**
     * Requestquote constructor.
     *
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param HelperData $helperData
     * @param HelperRule $helperRule
     * @param JsonFactory $resultJsonFactory
     * @param RequestsFactory $requestsFactory
     * @param ImageFactory $imageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        HelperData $helperData,
        HelperRule $helperRule,
        JsonFactory $resultJsonFactory,
        RequestsFactory $requestsFactory,
        ImageFactory $imageFactory
    )
    {
        $this->resultPageFactory = $resultPageFactory;
        $this->_helperData       = $helperData;
        $this->_helperRule       = $helperRule;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->requestsFactory   = $requestsFactory;
        $this->imageFactory      = $imageFactory;

        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Json|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Framework\View\Result\Page $resultPage */
        if (!$this->_helperData->isEnabled() || !$this->getRequest()->isAjax()) {
            return $this->resultJsonFactory->create()->setData(false);
        }
        $response = [];
        $data     = $this->getRequest()->getPostValue();
        if (!empty($data)) {
            $productId          = (int)$data["product_id"];
            $data["product_id"] = $productId;

            try {
                $storeId         = $this->_helperData->getStoreId();
                $product         = $this->_helperRule->getProductById($productId);
                $productSKU      = $product->getSku();
                $productName     = $product->getName();
                $productUrl      = $product->getProductUrl();
                $rankRequest     = $this->_helperRule->getRankRequests($productId);
                $productImageUrl = $this->getProductImage($product, 'mpcallforprice_image');

                /** if admin use custom request config status*/
                $customStateArray    = [];
                $chooseDefaultStatus = RequestState::TODO;
                $requestStateConfig  = $this->_helperData->getRequestStatusConfig();
                if (sizeof($requestStateConfig) > 0) {
                    foreach ($requestStateConfig as $keyroof => $customState) {
                        foreach ($customState as $key => $value) {
                            if ($key == 'labelstatus') {
                                $customStateArray[] = $value;
                            }
                            if ($key == 'isdefault') {
                                $chooseDefaultStatus = $keyroof;
                            }
                        }
                    }
                }

                $data['store_ids']    = $storeId;
                $data['item_product'] = $productName;
                $data['sku']          = $productSKU;
                $data['created_at']   = (new \DateTime())->format(DateTime::DATETIME_PHP_FORMAT);
                $data['status']       = $chooseDefaultStatus;
                $data['rank_request'] = $rankRequest;

                $this->requestsFactory->create()->addData($data)->save();

                $response = [
                    'error' => false,
                    'msg'   => __('Your request has been successfully submitted. We will contact you shortly.'),
                ];
                /**send email to admin*/
                if ($this->_helperData->getEmailEnableConfig()) {
                    $sendTo      = $this->_helperData->getEmailSendtoConfig();
                    $sendToArray = explode(',', $sendTo);
                    foreach ($sendToArray as $send) {
                        $this->_helperData->sendMail(
                            $send,
                            $data['name'],
                            $data['email'],
                            $data['phone'],
                            nl2br($data['customer_note']),
                            $productName,
                            $productUrl,
                            'mpcfp_request_quote_email_template',
                            $storeId,
                            $productImageUrl
                        );
                    }
                }
                /** send email to admin*/
            } catch (\Exception $e) {
                $response = [
                    'error' => true,
                    'msg'   => __('Something went wrong while saving data. Please try again later.'),
                ];
            }
        }

        return $this->resultJsonFactory->create()->setData($response);
    }

    /**
     * Retrieve product image
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param string $imageId
     *
     * @return string
     */
    public function getProductImage($product, $imageId)
    {
        $imageUrl = $this->imageFactory->create()
            ->init($product, $imageId)
            ->getUrl();

        return $imageUrl;
    }
}
