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
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\NotFoundException;

class GetCookies extends Action
{
    /**
     * @var \Extait\Cookie\Helper\Cookie
     */
    protected $cookieHelper;

    /**
     * GetCookies constructor.
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
     * Get allowed and disallowed by user cookies names.
     *
     * @return \Magento\Framework\Controller\ResultInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function execute()
    {
        /** @var \Magento\Framework\App\Request\Http $request */
        $request = $this->getRequest();

        if ($request->isPost() === false) {
            throw new NotFoundException(__('The request is not POST.'));
        }

        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);

        $this->messageManager->addSuccessMessage(__('Cookie Settings have been saved.'));

        return $resultJson->setData([
            'allowedCookies' => $this->cookieHelper->getUserAllowedCookiesNames(),
            'disallowedCookies' => $this->cookieHelper->getUserDisallowedCookiesNames(),
        ]);
    }
}
