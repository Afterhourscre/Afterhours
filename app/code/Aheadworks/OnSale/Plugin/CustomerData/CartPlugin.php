<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Plugin\CustomerData;

use Aheadworks\OnSale\Block\Label\Renderer as LabelRenderer;
use Aheadworks\OnSale\Block\Label\RendererFactory as LabelRendererFactory;
use Aheadworks\OnSale\Model\Source\Label\Renderer\Placement;
use Aheadworks\OnSale\Model\Source\Label\Renderer\Size;
use Magento\Checkout\CustomerData\Cart;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Item;
use Magento\Checkout\Model\Session;

/**
 * Class CartPlugin
 *
 * @package Aheadworks\OnSale\Plugin\CustomerData
 */
class CartPlugin
{
    /**
     * @var Session
     */
    private $checkoutSession;

    /**
     * @var Quote|null
     */
    private $quote = null;

    /**
     * @var LabelRendererFactory
     */
    private $labelRendererFactory;

    /**
     * @param Session $checkoutSession
     * @param LabelRendererFactory $labelRendererFactory
     */
    public function __construct(
        Session $checkoutSession,
        LabelRendererFactory $labelRendererFactory
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->labelRendererFactory = $labelRendererFactory;
    }

    /**
     * Add label html data for rendering to result
     *
     * @param Cart $subject
     * @param array $result
     * @return array
     */
    public function afterGetSectionData($subject, $result)
    {
        $items = $this->getQuote()->getAllVisibleItems();
        if (is_array($result['items'])) {
            foreach ($result['items'] as $key => $itemAsArray) {
                if ($item = $this->findItemById($itemAsArray['item_id'], $items)) {
                    /** @var LabelRenderer $labelRenderer */
                    $labelRenderer = $this->labelRendererFactory->create(
                        [
                            'data' => [
                                'placement' => Placement::MINICART,
                                'product' => $item->getProduct()
                            ]
                        ]
                    );

                    $result['items'][$key]['aw_onsale_label'] = $labelRenderer->toHtml();
                }
            }
        }

        return $result;
    }

    /**
     * Get active quote
     *
     * @return Quote
     */
    private function getQuote()
    {
        if (null === $this->quote) {
            $this->quote = $this->checkoutSession->getQuote();
        }
        return $this->quote;
    }

    /**
     * Find item by id in items haystack
     *
     * @param int $id
     * @param array $itemsHaystack
     * @return Item|bool
     */
    private function findItemById($id, $itemsHaystack)
    {
        if (is_array($itemsHaystack)) {
            foreach ($itemsHaystack as $item) {
                /** @var $item Item */
                if ((int)$item->getItemId() == $id) {
                    return $item;
                }
            }
        }
        return false;
    }
}
