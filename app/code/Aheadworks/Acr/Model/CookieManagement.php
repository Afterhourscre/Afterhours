<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Acr\Model;

use Magento\Framework\Stdlib\Cookie\PublicCookieMetadata;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Magento\Framework\Json\Helper\Data as JsonHelper;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;

/**
 * Class CookieManagement
 * @package Aheadworks\Acr\Model
 */
class CookieManagement
{
    /**
     * Section data ids
     */
    const SECTION_DATA_IDS = 'section_data_ids';

    /**
     * @var CookieManagerInterface
     */
    private $cookieManager;

    /**
     * @var CookieMetadataFactory
     */
    private $cookieMetadataFactory;

    /**
     * @var JsonHelper
     */
    private $jsonHelper;

    /**
     * @param CookieManagerInterface $cookieManager
     */
    public function __construct(
        CookieManagerInterface $cookieManager,
        CookieMetadataFactory $cookieMetadataFactory,
        JsonHelper $jsonHelper
    ) {
        $this->cookieManager = $cookieManager;
        $this->cookieMetadataFactory = $cookieMetadataFactory;
        $this->jsonHelper = $jsonHelper;
    }

    /**
     * Invalidate top cart section
     *
     * @return void
     */
    public function invalidateTopCart()
    {
        $sections = $this->getSectionsDetail();
        if (isset($sections['cart'])) {
            $sections['cart'] += 1000;
            $sectionsJson = $this->jsonEncode($sections);
            $metadata = $this->prepareMetadata();
            $this->deleteCookie($metadata);
            $this->setCookie($sectionsJson, $metadata);
        }
    }

    /**
     * Get sections detail
     *
     * @return array
     */
    private function getSectionsDetail()
    {
        $sectionsJson = $this->cookieManager->getCookie(self::SECTION_DATA_IDS);
        return $this->jsonHelper->jsonDecode($sectionsJson);
    }

    /**
     * Json Encode
     *
     * @param string $value
     * @return string
     */
    private function jsonEncode($value)
    {
        return $this->jsonHelper->jsonEncode($value);
    }

    /**
     * Prepare Metadata
     *
     * @return PublicCookieMetadata
     */
    private function prepareMetadata()
    {
        $metadata = $this->cookieMetadataFactory->createPublicCookieMetadata();
        $metadata->setPath('/');
        return $metadata;
    }

    /**
     * Set Public Cookie
     *
     * @param string $sectionsJson
     * @param PublicCookieMetadata|null $metadata
     */
    public function setCookie($sectionsJson, $metadata = null)
    {
        $this->cookieManager->setPublicCookie(self::SECTION_DATA_IDS, $sectionsJson, $metadata);
    }

    /**
     * Delete Cookie
     *
     * @param PublicCookieMetadata|null $metadata
     */
    public function deleteCookie($metadata = null)
    {
        $this->cookieManager->deleteCookie(self::SECTION_DATA_IDS, $metadata);
    }
}
