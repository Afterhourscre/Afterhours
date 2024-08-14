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

namespace Extait\Cookie\Controller\Settings;

use Extait\Cookie\Helper\Cookie as CookieHelper;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\NotFoundException;

class Index extends Action
{
    /**
     * @var \Extait\Cookie\Helper\Cookie
     */
    protected $cookieHelper;

    /**
     * Index constructor.
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Extait\Cookie\Helper\Cookie $cookieHelper
     */
    public function __construct(Context $context, CookieHelper $cookieHelper)
    {
        parent::__construct($context);

        $this->cookieHelper = $cookieHelper;
    }

    /**
     * Setting index page.
     *
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|\Magento\Framework\View\Result\Page
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function execute()
    {
        if ($this->cookieHelper->isModuleEnabled() === false) {
            throw new NotFoundException(__('Module is disabled'));
        }

        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->getConfig()->getTitle()->set(__('Cookie Settings'));

        return $resultPage;
    }
}
