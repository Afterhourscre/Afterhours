<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Block\Label;

use Aheadworks\OnSale\Api\Data\BlockInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Aheadworks\OnSale\Model\Label\Renderer\Product\Resolver as ProductResolver;
use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Customer\Model\Context as CustomerContext;
use Aheadworks\OnSale\ViewModel\Label\Renderer as RendererViewModel;

/**
 * Class Renderer
 *
 * @method string|null getPlacement()
 * @method string|null getImage()
 * @package Aheadworks\OnSale\Block\Label
 */
class Renderer extends Template
{
    /**
     * {@inheritdoc}
     */
    protected $_template = 'Aheadworks_OnSale::label/renderer.phtml';

    /**
     * @var ProductResolver
     */
    private $productResolver;

    /**
     * @var HttpContext
     */
    private $httpContext;

    /**
     * @var RendererViewModel
     */
    private $viewModel;

    /**
     * @param Context $context
     * @param ProductResolver $productResolver
     * @param HttpContext $httpContext
     * @param RendererViewModel $viewModel
     * @param array $data
     */
    public function __construct(
        Context $context,
        ProductResolver $productResolver,
        HttpContext $httpContext,
        RendererViewModel $viewModel,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->productResolver = $productResolver;
        $this->httpContext = $httpContext;
        $this->viewModel = $viewModel;
    }

    /**
     * Get view model for block
     *
     * @return RendererViewModel
     */
    public function getViewModel()
    {
        return $this->viewModel;
    }

    /**
     * Retrieve label blocks for area
     *
     * @param string $area
     * @return BlockInterface[]
     */
    public function getLabelBlocksForArea($area)
    {
        $labelBlocks = $this->getViewModel()->getLabelBlocksForArea(
            $area,
            $this->getPlacement(),
            $this->getProduct(),
            $this->getCustomerGroupId()
        );
        return $labelBlocks;
    }

    /**
     * @inheritdoc
     */
    protected function _toHtml()
    {
        $labelBlocks = $this->getViewModel()->getLabelBlocks(
            $this->getPlacement(),
            $this->getProduct(),
            $this->getCustomerGroupId()
        );

        if ($this->getProduct() && !empty($labelBlocks)) {
            return parent::_toHtml();
        }
        return '';
    }

    /**
     * Get product
     *
     * @return Product|ProductInterface|null
     */
    public function getProduct()
    {
        $product = $this->getData('product');
        return $product === null
            ? $this->productResolver->resolveByPlacement($this->getPlacement())
            : $product ;
    }

    /**
     * Get customer group ID
     *
     * @return null
     */
    public function getCustomerGroupId()
    {
        $customerGroupId = $this->getData('customer_group_id');
        return $customerGroupId === null
            ? $this->httpContext->getValue(CustomerContext::CONTEXT_GROUP)
            : $customerGroupId;
    }
}
