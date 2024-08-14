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

namespace Mageplaza\CallForPrice\Plugin\Product\Model;

use Magento\Catalog\Model\Product as ProductModel;
use Mageplaza\CallForPrice\Helper\Data as HelperData;
use Mageplaza\CallForPrice\Helper\Rule as HelperRule;

/**
 * Class Product
 * @package Mageplaza\CallForPrice\Plugin\Product\Model
 */
class Product
{
    /**
     * @var HelperData
     */
    protected $helperData;

    /**
     * @var HelperRule
     */
    protected $helperRule;

    /**
     * Product constructor.
     *
     * @param HelperData $helperData
     * @param HelperRule $helperRule
     */
    public function __construct(
        HelperData $helperData,
        HelperRule $helperRule
    )
    {
        $this->helperData = $helperData;
        $this->helperRule = $helperRule;
    }

    /**
     * @param ProductModel $productModel
     * @param              $name
     *
     * @return bool
     */
    public function aroundIsSaleable(ProductModel $productModel, $name)
    {
        if (!$this->helperData->isEnabled()) {
            return $productModel->getIsSalable();
        }

        $productId                      = $productModel->getId();
        $validateProductInRuleAvailable = $this->helperRule->validateProductInRuleAvailable($productId);
        if ($validateProductInRuleAvailable) {
            $action = $validateProductInRuleAvailable->getAction();
            /** return getIsSalable with action is login to see price*/
            if ($action == 'login_see_price' && $this->helperData->getCustomerLogedIn()) {
                return $productModel->getIsSalable();
            }

            /** return false to hide add to cart button*/
            return false;
        }

        return $productModel->getIsSalable();
    }
}
