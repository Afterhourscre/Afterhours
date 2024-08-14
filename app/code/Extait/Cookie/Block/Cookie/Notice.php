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

namespace Extait\Cookie\Block\Cookie;

use Extait\Cookie\Helper\Cookie as CookieHelper;
use Magento\Cookie\Block\Html\Notices;
use Magento\Framework\View\Element\Template;

/** @api */
class Notice extends Notices
{
    /**
     * @var \Extait\Cookie\Helper\Cookie
     */
    protected $cookieHelper;

    /**
     * Notice constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Extait\Cookie\Helper\Cookie $cookieHelper
     * @param array $data
     */
    public function __construct(Template\Context $context, CookieHelper $cookieHelper, array $data = [])
    {
        parent::__construct($context, $data);

        $this->cookieHelper = $cookieHelper;
    }

    /**
     * @inheritdoc
     */
    public function getTemplate()
    {
        return $this->cookieHelper->isModuleEnabled() ? 'Extait_Cookie::cookie/notice.phtml' : parent::getTemplate();
    }

    /**
     * Check whether the module is enabled or not
     *
     * @return bool
     */
    public function isModuleEnabled()
    {
        return $this->cookieHelper->isModuleEnabled();
    }

    /**
     * Get a cookie restriction message.
     *
     * @return string
     */
    public function getCookieRestrictionMessage()
    {
        return $this->cookieHelper->getCookieRestrictionMessage();
    }

    /**
     * Get the cookie settings URL.
     *
     * @return string
     */
    public function getCookieSettingsUrl()
    {
        return $this->_urlBuilder->getUrl('cookie/settings/index');
    }
}
