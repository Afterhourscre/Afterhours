<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */


namespace Aheadworks\Faq\Controller;

use Aheadworks\Faq\Model\Config;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Phrase;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class AbstractAction
 */
abstract class AbstractAction extends Action
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * AbstractAction constructor
     *
     * @param Context $context
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(Context $context, StoreManagerInterface $storeManager)
    {
        $this->storeManager = $storeManager;
        parent::__construct($context);
    }

    /**
     * Dispatch request
     *
     * @return ResponseInterface|ResultInterface
     * @throws \Exception
     */
    public function execute()
    {
        if (!$this->config instanceof Config) {
            throw new \Exception(__('Config must be instance of %1', Config::class));
        }

        if ($this->config->isDisabledFaqForCurrentCustomer()) {
            return $this->redirectWithErrorMessage();
        }

        return $this->_execute();
    }

    /**
     * Get current store ids
     *
     * @return array
     */
    protected function getCurrentStores()
    {
        return [Store::DEFAULT_STORE_ID, $this->getCurrentStore()];
    }

    /**
     * Get current store id
     *
     * @return int
     */
    protected function getCurrentStore()
    {
        return (int)$this->storeManager->getStore()->getId();
    }

    /**
     * Set error message and redirect to index page
     *
     * @param $message Phrase - Optional, default = 'Access Denied'
     * @return Redirect
     */
    protected function redirectWithErrorMessage($message = null)
    {
        if (!$message) {
            $message = __('Access Denied');
        }

        $this->messageManager->addErrorMessage($message);
        return $this->resultRedirectFactory->create()->setPath('/');
    }

    /**
     * Dispatch request
     *
     * @return \Magento\Framework\Controller\ResultInterface|ResponseInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    abstract protected function _execute();
}
