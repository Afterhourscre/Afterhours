<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the commercial license
 * that is bundled with this package in the file LICENSE.txt.
 *
 * @category Extait
 * @package Extait_Cookie
 * @copyright Copyright (c) 2016-2018 Extait, Inc. (http://www.extait.com)
 */

namespace Extait\Cookie\Controller\Ajax;

use Extait\Cookie\Helper\Cookie as CookieHelper;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\Action;
use Magento\Framework\Controller\ResultFactory;

class AddCookie extends Action
{
    /**
     * @var \Extait\Cookie\Helper\Cookie
     */
    protected $cookieHelper;

    /**
     * AddCookie constructor.
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Extait\Cookie\Helper\Cookie $cookieHelper
     */
    public function __construct(Context $context, CookieHelper $cookieHelper)
    {
        parent::__construct($context);

        $this->cookieHelper = $cookieHelper;
    }

    /**
     * Create an empty cookie entity.
     *
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $cookieName = $this->getRequest()->getParam('cookie');

        $this->cookieHelper->createEmptyCookie($cookieName);

        return $resultJson->setData(['success' => true]);
    }
}
