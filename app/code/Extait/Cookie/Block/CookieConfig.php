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

namespace Extait\Cookie\Block;

use Extait\Cookie\Helper\Cookie;
use Magento\Framework\Json\EncoderInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Cookie\Helper\Cookie as MageCookieHelper;

/** @api */
class CookieConfig extends Template
{
    /**
     * @var \Extait\Cookie\Helper\Cookie
     */
    protected $cookieHelper;

    /**
     * @var \Magento\Cookie\Helper\Cookie
     */
    protected $mageCookieHelper;

    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    protected $jsonEncoder;

    /**
     * CookieConfig constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Extait\Cookie\Helper\Cookie $cookieHelper
     * @param \Magento\Cookie\Helper\Cookie $mageCookieHelper
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param array $data
     */
    public function __construct(
        Context $context,
        Cookie $cookieHelper,
        MageCookieHelper $mageCookieHelper,
        EncoderInterface $jsonEncoder,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->cookieHelper = $cookieHelper;
        $this->mageCookieHelper = $mageCookieHelper;
        $this->jsonEncoder = $jsonEncoder;
    }

    /**
     * @return string
     */
    public function getSerializedConfig()
    {
        return $this->jsonEncoder->encode([
            'isModuleEnable' => $this->cookieHelper->isModuleEnabled(),
            'allCookiesNames' => $this->cookieHelper->getAllCookiesNames(),
            'addCookieUrl' => $this->getUrl('cookie/ajax/addCookie'),
            'mageCookieSettings' => [
                'cookieName' => MageCookieHelper::IS_USER_ALLOWED_SAVE_COOKIE,
                'cookieValue' => $this->mageCookieHelper->getAcceptedSaveCookiesWebsiteIds(),
                'cookieLifetime' => $this->mageCookieHelper->getCookieRestrictionLifetime(),
            ],
        ]);
    }
}
