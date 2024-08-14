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

namespace Mageplaza\CallForPrice\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class Rules
 * @package Mageplaza\CallForPrice\Controller\Adminhtml
 */
abstract class Requests extends Action
{
    const ADMIN_RESOURCE = 'Mageplaza_CallForPrice::requests';

    /**
     * @type PageFactory
     */
    protected $_resultPageFactory;

    /**
     * @type Registry
     */
    protected $_coreRegistry;

    /**
     * Rules constructor.
     *
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param Registry $coreRegistry
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        Registry $coreRegistry
    )
    {
        $this->_coreRegistry      = $coreRegistry;
        $this->_resultPageFactory = $resultPageFactory;

        parent::__construct($context);
    }

    /**
     * @return \Mageplaza\CallForPrice\Model\Rules
     */
    protected function _initRequest()
    {
        $requestId = (int)$this->getRequest()->getParam('request_id');
        /** @var \Mageplaza\CallForPrice\Model\Rules $rule */
        $request = $this->_objectManager->create('Mageplaza\CallForPrice\Model\Requests');
        if ($requestId) {
            $request->load($requestId);
        }
        if (!$this->_coreRegistry->registry('current_request')) {
            $this->_coreRegistry->register('current_request', $request);
        }

        return $request;
    }
}
