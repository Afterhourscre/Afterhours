<?php

namespace MageCloud\PayByLink\Controller\Adminhtml\Index;

use Mageplaza\Barclaycard\Gateway\Config\Direct;
use Mageplaza\Barclaycard\Helper\Request;
use Psr\Log\LoggerInterface;

class Generate extends \Magento\Backend\App\Action
{

    protected $resultPageFactory;
    protected $jsonHelper;
    protected $helper;
    /**
     * @var Request
     */
    private $requestHelper;

    /**
     * @var Direct
     */
    private $config;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     * @param \MageCloud\PayByLink\Helper\Data $helper
     * @param Request $requestHelper
     * @param Direct $config
     * @param LoggerInterface $logger
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \MageCloud\PayByLink\Helper\Data $helper,
        Request $requestHelper,
        Direct $config,
        LoggerInterface $logger
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->jsonHelper = $jsonHelper;
        $this->helper = $helper;
        $this->requestHelper = $requestHelper;
        $this->config = $config;
        $this->logger = $logger;
        parent::__construct($context);
    }

    /**
     * Execute view action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $request = $this->getRequest();
        $storeId = $request->getParam('websiteId');
        $this->config->setMethodCode('mpbarclaycard_hosted');

        $shaIn = $request->getParam('sha_in');
        if ($shaIn === '******') {
            $shaIn = $this->config->getShaIn($storeId);
        }

        $amount = (float)$request->getParam('amount') * 100;
        $url  = $this->requestHelper->getApiUrl(\Mageplaza\Barclaycard\Helper\Request::HOSTED);

        $body = [
            'PSPID'     => $request->getParam('psp_id'),
            'ORDERID'   => $request->getParam('order_number'),
            'AMOUNT'    => $amount,
            'CURRENCY'  => 'GBP',
            'LANGUAGE' => 'en_US'
        ];

        $this->requestHelper->appendShaSign($body, $shaIn, $request->getParam('hash_algorithm'));

        $response = $url .'?' . $this->helper->toString($body);

        try {
            return $this->jsonResponse(['type' => 'success', 'message' =>$response]);
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            return $this->jsonResponse($e->getMessage());
        } catch (\Exception $e) {
            $this->logger->critical($e);
            return $this->jsonResponse($e->getMessage());
        }
    }

    /**
     * Create json response
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function jsonResponse($response = '')
    {
        return $this->getResponse()->representJson(
            $this->jsonHelper->jsonEncode($response)
        );
    }
}
