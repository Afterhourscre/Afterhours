<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Acr\Api;

/**
 * Interface CartRestoreManagementInterface
 * @package Aheadworks\Acr\Api
 */
interface CartRestoreManagementInterface
{
    /**
     * Save restore code
     *
     * @param int $cartHistoryId
     * @param int $quoteId
     */
    public function saveRestoreCode($eventHistoryId, $quoteId);

    /**
     * Get cart restore item by history id
     *
     * @param int $eventHistoryId
     * @return \Aheadworks\Acr\Api\Data\CartRestoreInterface
     */
    public function getCartRestoreItemByHistoryId($eventHistoryId);
}
