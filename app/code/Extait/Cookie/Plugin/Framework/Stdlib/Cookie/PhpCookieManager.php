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

namespace Extait\Cookie\Plugin\Framework\Stdlib\Cookie;

use Extait\Cookie\Helper\Cookie as CookieHelper;
use Magento\Framework\Json\Decoder;
use Magento\Framework\Stdlib\Cookie\PublicCookieMetadata;
use Magento\Framework\Stdlib\Cookie\PhpCookieManager as Subject;

class PhpCookieManager
{
    /**
     * @var \Extait\Cookie\Helper\Cookie
     */
    protected $cookieHelper;

    /**
     * @var \Magento\Framework\Json\Decoder
     */
    protected $jsonDecoder;

    /**
     * PhpCookieManager constructor.
     *
     * @param \Extait\Cookie\Helper\Cookie $cookieHelper
     * @param \Magento\Framework\Json\Decoder $jsonDecoder
     */
    public function __construct(CookieHelper $cookieHelper, Decoder $jsonDecoder)
    {
        $this->cookieHelper = $cookieHelper;
        $this->jsonDecoder = $jsonDecoder;
    }

    /**
     * Check if the cookie is allowed by user before set.
     *
     * @param \Magento\Framework\Stdlib\Cookie\PhpCookieManager $subject
     * @param $proceed
     * @param $name
     * @param $value
     * @param \Magento\Framework\Stdlib\Cookie\PublicCookieMetadata|null $metadata
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function aroundSetPublicCookie(
        Subject $subject,
        $proceed,
        $name,
        $value,
        PublicCookieMetadata $metadata = null
    ) {
        if ($this->cookieHelper->isModuleEnabled()) {
            $allCookies = $this->cookieHelper->getAllCookiesNames();

            if (!in_array($name, $allCookies)) {
                $this->cookieHelper->createEmptyCookie($name);
            }

            $allowedCookies = $this->cookieHelper->getUserAllowedCookiesNames();

            if (!in_array($name, $allowedCookies)) {
                return;
            }
        }

        $proceed($name, $value, $metadata);
    }
}
