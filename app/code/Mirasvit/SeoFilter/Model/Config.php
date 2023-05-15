<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-seo-filter
 * @version   1.0.16
 * @copyright Copyright (C) 2020 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\SeoFilter\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Store\Model\ScopeInterface;

class Config
{
    const FILTER_NAME_WITHOUT_SEPARATOR        = 0;
    const FILTER_NAME_BOTTOM_DASH_SEPARATOR    = 1;
    const FILTER_NAME_CAPITAL_LETTER_SEPARATOR = 2;

    const SEPARATOR_FILTER_VALUES = ',';
    const SEPARATOR_FILTERS       = '-';
    const SEPARATOR_DECIMAL       = ':';

    const FILTER_STOCK  = 'stock';
    const FILTER_SALE   = 'on_sale';
    const FILTER_NEW    = 'new_products';
    const FILTER_RATING = 'rating';

    const LABEL_STOCK_IN  = 'instock';
    const LABEL_STOCK_OUT = 'outofstock';

    const LABEL_RATING_1 = 'rating1';
    const LABEL_RATING_2 = 'rating2';
    const LABEL_RATING_3 = 'rating3';
    const LABEL_RATING_4 = 'rating4';
    const LABEL_RATING_5 = 'rating5';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    private $request;

    /**
     * Config constructor.
     * @param ScopeConfigInterface $scopeConfig
     * @param RequestInterface $request
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        RequestInterface $request
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->request     = $request;
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->scopeConfig->getValue('seofilter/seofilter/is_seofilter_enabled', ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return bool
     */
    public function isApplicable()
    {
        return $this->isEnabled() && $this->request->getFullActionName() === 'catalog_category_view';
    }

    /**
     * @return int
     */
    public function getComplexFilterNamesSeparator()
    {
        return $this->scopeConfig->getValue('seofilter/seofilter/complex_seofilter_names_separator', ScopeInterface::SCOPE_STORE);
    }

    public function getCustomSeparator()
    {
        return $this->scopeConfig->getValue('seofilter/seofilter/custom_separator', ScopeInterface::SCOPE_STORE);
    }
}
