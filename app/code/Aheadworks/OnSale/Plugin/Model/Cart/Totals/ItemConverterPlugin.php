<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Plugin\Model\Cart\Totals;

use Aheadworks\OnSale\Block\Label\Renderer as LabelRenderer;
use Aheadworks\OnSale\Block\Label\RendererFactory as LabelRendererFactory;
use Magento\Quote\Api\Data\TotalsItemExtensionInterfaceFactory;
use Aheadworks\OnSale\Model\Source\Label\Renderer\Placement;
use Magento\Quote\Api\Data\TotalsItemInterface;
use Magento\Quote\Model\Quote\Item;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Cart\Totals\ItemConverter;

/**
 * Class ItemConverterPlugin
 *
 * @package Aheadworks\OnSale\Plugin\Model\Cart\Totals
 */
class ItemConverterPlugin
{
    /**
     * @var LabelRendererFactory
     */
    private $labelRendererFactory;

    /**
     * @var TotalsItemExtensionInterfaceFactory
     */
    private $totalsItemExtensionFactory;

    /**
     * @param LabelRendererFactory $labelRendererFactory
     * @param TotalsItemExtensionInterfaceFactory $totalsItemExtensionFactory
     */
    public function __construct(
        LabelRendererFactory $labelRendererFactory,
        TotalsItemExtensionInterfaceFactory $totalsItemExtensionFactory
    ) {
        $this->labelRendererFactory = $labelRendererFactory;
        $this->totalsItemExtensionFactory = $totalsItemExtensionFactory;
    }

    /**
     * Add label html data for rendering to total items
     *
     * @param ItemConverter $subject
     * @param TotalsItemInterface $resultItemsData
     * @param Item $item
     * @return TotalsItemInterface
     */
    public function afterModelToDataObject($subject, $resultItemsData, $item)
    {
        $extensionAttributes = $resultItemsData->getExtensionAttributes()
            ? $resultItemsData->getExtensionAttributes()
            : $this->totalsItemExtensionFactory->create();

        $extensionAttributes->setAwOnsaleLabel($this->getLabelHtml($item, $item->getQuote()));
        $resultItemsData->setExtensionAttributes($extensionAttributes);

        return $resultItemsData;
    }

    /**
     * Get onsale label html
     *
     * @param Item $item
     * @param Quote $quote
     * @return string
     */
    private function getLabelHtml($item, $quote)
    {
        /** @var LabelRenderer $labelRenderer */
        $labelRenderer = $this->labelRendererFactory->create(
            [
                'data' => [
                    'customer_group_id' => $quote->getCustomerGroupId(),
                    'placement' => Placement::CHECKOUT,
                    'product' => $item->getProduct()
                ]
            ]
        );

        return $labelRenderer->toHtml();
    }
}
