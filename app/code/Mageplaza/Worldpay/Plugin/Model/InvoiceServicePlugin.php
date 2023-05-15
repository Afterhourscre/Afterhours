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
 * @package     Mageplaza_Worldpay
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\Worldpay\Plugin\Model;

use Closure;
use Exception;
use Magento\Framework\App\Area;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\App\Request\Http;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\State;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Api\Data\InvoiceItemInterface;
use Magento\Sales\Api\Data\OrderItemInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Invoice;
use Magento\Sales\Model\Order\Invoice\Item as InvoiceItem;
use Magento\Sales\Model\Order\Item as OrderItem;
use Magento\Sales\Model\Service\InvoiceService;

/**
 * Class InvoiceServicePlugin
 * @package Mageplaza\Worldpay\Plugin\Model
 */
class InvoiceServicePlugin
{
    /**
     * @var RequestInterface|Http
     */
    private $request;

    /**
     * @var State
     */
    private $state;

    /**
     * @var ProductMetadataInterface
     */
    private $productMetadata;

    /**
     * @var \Magento\Sales\Model\Convert\Order
     */
    private $orderConverter;

    /**
     * InvoiceServicePlugin constructor.
     *
     * @param State $state
     * @param RequestInterface $request
     * @param ProductMetadataInterface $productMetadata
     * @param \Magento\Sales\Model\Convert\Order $orderConverter
     */
    public function __construct(
        State $state,
        RequestInterface $request,
        ProductMetadataInterface $productMetadata,
        \Magento\Sales\Model\Convert\Order $orderConverter
    ) {
        $this->state = $state;
        $this->request = $request;
        $this->productMetadata = $productMetadata;
        $this->orderConverter = $orderConverter;
    }

    /**
     * @param InvoiceService $subject
     * @param Closure $proceed
     * @param Order $order
     * @param array $qtys
     *
     * @return Invoice
     * @throws LocalizedException
     * @throws Exception
     */
    public function aroundPrepareInvoice(InvoiceService $subject, Closure $proceed, Order $order, array $qtys = [])
    {
        $route = $this->request->getRouteName();
        $areaCode = $this->state->getAreaCode();
        $is231 = version_compare($this->productMetadata->getVersion(), '2.3.1', '=');

        if (!$is231 || ($route !== 'mpworldpay' && $areaCode !== Area::AREA_WEBAPI_REST)) {
            return $proceed($order, $qtys);
        }

        $invoice = $this->orderConverter->toInvoice($order);
        $totalQty = 0;
        foreach ($order->getAllItems() as $orderItem) {
            if (!$this->_canInvoiceItem($orderItem, $qtys)) {
                continue;
            }
            $item = $this->orderConverter->itemToInvoiceItem($orderItem);
            if (isset($qtys[$orderItem->getId()])) {
                $qty = (double) $qtys[$orderItem->getId()];
            } elseif ($orderItem->isDummy()) {
                $qty = $orderItem->getQtyOrdered() ?: 1;
            } elseif (empty($qtys)) {
                $qty = $orderItem->getQtyToInvoice();
            } else {
                $qty = 0;
            }
            $totalQty += $qty;
            $this->setInvoiceItemQuantity($item, $qty);
            $invoice->addItem($item);
        }
        $invoice->setTotalQty($totalQty);
        $invoice->collectTotals();
        $order->getInvoiceCollection()->addItem($invoice);

        return $invoice;
    }

    /**
     * Check if order item can be invoiced.
     *
     * @param OrderItemInterface|OrderItem $item
     * @param array $qtys
     *
     * @return bool
     */
    protected function _canInvoiceItem(OrderItemInterface $item, array $qtys = [])
    {
        if ($item->getLockedDoInvoice()) {
            return false;
        }

        if ($item->isDummy()) {
            if ($item->getHasChildren() && is_array($item->getChildrenItems())) {
                /** @var OrderItem $child */
                foreach ($item->getChildrenItems() as $child) {
                    if (empty($qtys)) {
                        if ($child->getQtyToInvoice() > 0) {
                            return true;
                        }
                    } elseif (isset($qtys[$child->getId()]) && $qtys[$child->getId()] > 0) {
                        return true;
                    }
                }

                return false;
            }

            /** @var OrderItem $parent */
            if ($parent = $item->getParentItem()) {
                if (empty($qtys)) {
                    return $parent->getQtyToInvoice() > 0;
                }

                return isset($qtys[$parent->getId()]) && $qtys[$parent->getId()] > 0;
            }
        } else {
            return $item->getQtyToInvoice() > 0;
        }

        return false;
    }

    /**
     * Set quantity to invoice item
     *
     * @param InvoiceItemInterface|InvoiceItem $item
     * @param float|int|string $qty
     *
     * @return $this
     * @throws LocalizedException
     */
    protected function setInvoiceItemQuantity(InvoiceItemInterface $item, $qty)
    {
        $qty = $item->getOrderItem()->getIsQtyDecimal() ? (double) $qty : (int) $qty;

        $qty = $qty > 0 ? $qty : 0;

        /**
         * Check qty availability
         */
        $qtyToInvoice = sprintf('%F', $item->getOrderItem()->getQtyToInvoice());
        $qty = sprintf('%F', $qty);
        if ($qty > $qtyToInvoice && !$item->getOrderItem()->isDummy()) {
            throw new LocalizedException(
                __('We found an invalid quantity to invoice item "%1".', $item->getName())
            );
        }

        $item->setQty($qty);

        return $this;
    }
}
