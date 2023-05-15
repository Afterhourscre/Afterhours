<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Acr\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface CartHistoryInterface
 * @package Aheadworks\Acr\Api\Data
 */
interface CartHistoryInterface extends ExtensibleDataInterface
{
    /**#@+
     * Constants defined for keys of the data array.
     * Identical to the name of the getter in snake case
     */
    const ID            = 'id';
    const REFERENCE_ID  = 'reference_id';
    const CART_DATA     = 'cart_data';
    const TRIGGERED_AT  = 'triggered_at';
    const PROCESSED     = 'processed';
    /**#@-*/

    /**
     * Get cart history ID
     *
     * @return int|null
     */
    public function getId();

    /**
     * Set cart history ID
     *
     * @param int $cartHistoryId
     * @return $this
     */
    public function setId($cartHistoryId);

    /**
     * Get reference ID
     *
     * @return int
     */
    public function getReferenceId();

    /**
     * Set reference ID
     *
     * @param int $referenceId
     * @return $this
     */
    public function setReferenceId($referenceId);

    /**
     * Get cart data (serialized)
     *
     * @return string
     */
    public function getCartData();

    /**
     * Set cart data (serialized)
     *
     * @param string $cartData
     * @return $this
     */
    public function setCartData($cartData);

    /**
     * Get trigger time
     *
     * @return string
     */
    public function getTriggeredAt();

    /**
     * Set trigger time
     *
     * @param string $triggeredAt
     * @return $this
     */
    public function setTriggeredAt($triggeredAt);

    /**
     * Get processed
     *
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getProcessed();

    /**
     * Set status
     *
     * @param bool $processed
     * @return $this
     */
    public function setProcessed($processed);

    /**
     * Retrieve existing extension attributes object or create a new one
     *
     * @return \Aheadworks\Acr\Api\Data\CartHistoryExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object
     *
     * @param \Aheadworks\Acr\Api\Data\CartHistoryExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(CartHistoryExtensionInterface $extensionAttributes);
}
