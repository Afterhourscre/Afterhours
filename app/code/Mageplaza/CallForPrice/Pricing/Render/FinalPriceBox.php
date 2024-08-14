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

namespace Mageplaza\CallForPrice\Pricing\Render;

use Mageplaza\CallForPrice\Helper\Data as HelperData;
use Mageplaza\CallForPrice\Helper\Rule as HelperRule;

/**
 * Class FinalPriceBox
 * @package Mageplaza\CallForPrice\Pricing\Render
 */
class FinalPriceBox
{
    /**
     * @var HelperData
     */
    protected $_helperData;

    /**
     * @var HelperRule
     */
    protected $_helperRule;

    /**
     * FinalPriceBox constructor.
     *
     * @param HelperData $helperData
     * @param HelperRule $helperRule
     */
    public function __construct(
        HelperData $helperData,
        HelperRule $helperRule
    )
    {
        $this->_helperData = $helperData;
        $this->_helperRule = $helperRule;
    }

    /**
     * @param string $html
     *
     * @return null|string
     */
    public function afterToHtml($subject, $html)
    {
        $isLoggedIn = $this->_helperData->getCustomerLogedIn();

        $helperDataCFP         = $this->_helperData;
        $productId             = $subject->getSaleableItem()->getId();
        $validateProductInRule = $this->_helperRule->validateProductInRuleAvailable($productId);
        if (!$validateProductInRule || !$helperDataCFP->isEnabled()) {
            return $html;
        }

        /** case action is hidden add to cart button*/
        $style_cfp_html = <<<STYLE
<style type="text/css">
    .stock.available span {
        display: none !important;
    }
    .product-item-actions .actions-primary, .mp_tac_label {
        display: inline !important;
    }
</style>
STYLE;

        /** return button call for price*/
        $action = $validateProductInRule->getAction();
        if ($action != 'hide_add_to_cart') {
            /** case actions are login to see price, popup get quote and redirect url*/
            if ($action == 'login_see_price' && $isLoggedIn) {
                return $html;
            }

            if (strpos($html, 'price-tier_price') !== false) {
                return '';
            }

            $buttonTitle = $validateProductInRule->getButtonLabel() ?: __('Call For Price');
            $button_cfp_html = <<<HTML
<div class="callforprice-action callforprice-action-{$productId}">
    <button type="button" title="{$buttonTitle}" class="action tocart primary" id="product-callforprice-{$productId}">
        <span>{$buttonTitle}</span>
    </button>
</div>
HTML;
            $jstranfer = <<<SCRIPT
<script type="text/x-magento-init">
{
    ".callforprice-action-{$productId}": {
        "validation": {},
        "Mageplaza_CallForPrice/js/rule":{
            "productId": "{$productId}",
            "action": "{$action}",
            "url_redirect_type": "{$validateProductInRule->getUrlRedirect()}",
            "customer_loged_in": "{$helperDataCFP->getCustomerLogedIn()}",
            "show_fields": "{$validateProductInRule->getShowFields()}",
            "required_fields": "{$validateProductInRule->getRequiredFields()}",
            "tac_check_default": "{$helperDataCFP->getTACDefaultCheckedConfig()}",
            "tac_required": "{$helperDataCFP->getTACRequiredConfig()}",
            "tac_label": "{$helperDataCFP->getTACLabel()}",
            "request_form_url": "{$helperDataCFP->getRequestQuoteUrl()}",
            "enable_terms": "{$validateProductInRule->getEnableTerms()}",
            "customer_group_id": "{$helperDataCFP->getCustomerGroupId()}",
            "quote_heading": "{$validateProductInRule->getQuoteHeading()}",
            "loginUrl": "{$helperDataCFP->getLoginUrl()}",
            "quote_description": "{$validateProductInRule->getQuoteDescription()}"
        }
    }
}
</script>
SCRIPT;

            return $button_cfp_html . $jstranfer . $style_cfp_html;
        }

        return $html . $style_cfp_html;
    }
}