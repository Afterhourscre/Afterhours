<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Helpdesk\Model\QuickResponse;

use Magento\Framework\Validator\AbstractValidator;
use Aheadworks\Helpdesk\Api\Data\QuickResponseInterface;

/**
 * Class Validator
 * @package Aheadworks\Helpdesk\Model\QuickResponse
 */
class Validator extends AbstractValidator
{
    /**
     * Returns true if quick response entity meets the validation requirements
     *
     * @param QuickResponseInterface $quickResponse
     * @return bool
     * @throws \Zend_Validate_Exception
     */
    public function isValid($quickResponse)
    {
        $this->_clearMessages();
        if (!$this->isQuickResponseDataValid($quickResponse)) {
            return false;
        }

        return true;
    }

    /**
     * Returns true if quick response data is correct
     *
     * @param QuickResponseInterface $quickResponse
     * @return bool
     * @throws \Zend_Validate_Exception
     */
    private function isQuickResponseDataValid($quickResponse)
    {
        $responseStoreIds = [];
        if ($quickResponse->getStoreResponseValues() && (is_array($quickResponse->getStoreResponseValues()))) {
            /** @var \Aheadworks\Helpdesk\Api\Data\StoreValueInterface $storeResponseValue */
            foreach ($quickResponse->getStoreResponseValues() as $storeResponseValue) {
                if (!in_array($storeResponseValue->getStoreId(), $responseStoreIds)) {
                    array_push($responseStoreIds, $storeResponseValue->getStoreId());
                } else {
                    $this->_addMessages([__('Duplicated store view in quick response found.')]);
                    return false;
                }
            }
        }
        return true;
    }
}
