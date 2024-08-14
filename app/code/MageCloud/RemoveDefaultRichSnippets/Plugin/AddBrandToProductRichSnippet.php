<?php

declare(strict_types=1);

namespace MageCloud\RemoveDefaultRichSnippets\Plugin;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Mirasvit\SeoMarkup\Block\Rs\Product as Subject;

class AddBrandToProductRichSnippet
{
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * AddBrandToProductRichSnippet constructor.
     *
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @return string
     */
    public function getStoreName(): string
    {
        return $this->scopeConfig->getValue(
            'general/store_information/name',
            ScopeInterface::SCOPE_STORE
        ) ?:'';
    }

    /**
     * @param Subject $subject
     * @param array $return
     *
     * @return array
     */
    public function afterGetJsonData(
        Subject $subject,
        ?array $return
    ): ?array {
        if (!isset($return['brand'])) {
            $return['brand'] = "After Hours Creative Studio";
        }

        return $return;
    }
}