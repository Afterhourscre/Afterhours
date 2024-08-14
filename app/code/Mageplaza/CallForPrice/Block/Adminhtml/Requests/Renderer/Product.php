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

namespace Mageplaza\CallForPrice\Block\Adminhtml\Requests\Renderer;

use Magento\Catalog\Model\ProductRepository;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Registry;

/**
 * Class Product
 * @package Mageplaza\CallForPrice\Block\Adminhtml\Requests\Renderer
 */
class Product extends AbstractElement
{
    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_productRepository;

    /**
     * @var Registry
     */
    protected $_coreRegistry;

    /**
     * Product constructor.
     *
     * @param ProductRepository $productRepository
     * @param Registry $registry
     */
    public function __construct(
        ProductRepository $productRepository,
        Registry $registry
    )
    {
        $this->_productRepository = $productRepository;
        $this->_coreRegistry      = $registry;
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getElementHtml()
    {
        $html = '';

        $requestData = $this->_coreRegistry->registry('current_request');
        $productId   = $requestData->getProductId();
        if ($productId != '') {
            $product     = $this->_productRepository->getById($productId);
            $productName = $product->getName();
            $productUrl  = $product->getProductUrl();
            $label       = __("Product Requested");

            $html = '<div class="control-value" style="padding-top: 8px; position: relative;"> <span style="font-weight: 600; position: absolute; left: -163px;">' . $label . '</span>';
            $html .= '<span><a href=' . $productUrl . ' target="_blank" style="text-decoration:underline;">' . $productName . '</a></span>';
            $html .= '</div>';
        }

        return $html;
    }
}