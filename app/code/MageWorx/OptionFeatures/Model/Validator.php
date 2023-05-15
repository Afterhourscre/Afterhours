<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\OptionFeatures\Model;

use Magento\Catalog\Api\Data\CustomOptionInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product\Option\Type\DefaultType;
use MageWorx\OptionBase\Api\ValidatorInterface;

class Validator implements ValidatorInterface
{
    /**
     * Run validation process for add to cart action, throws exception for selection limits
     *
     * @param DefaultType $subject
     * @param array $values
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return bool
     */
    public function canValidateAddToCart($subject, $values)
    {
        $option = $subject->getOption();
        if (isset($values[$option->getOptionId()]) && is_array($values[$option->getOptionId()])) {
            $selectionCounter = count($values[$option->getOptionId()]);
            if (!$option->getSelectionLimitFrom() && !$option->getSelectionLimitTo()) {
                return true;
            }
            if ($option->getSelectionLimitFrom() > $selectionCounter
                || $option->getSelectionLimitTo() < $selectionCounter
            ) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __(
                        "Please, choose required number of values for option '%1'.",
                        $option->getTitle()
                    )
                );
            }
        }

        return true;
    }

    /**
     * Run validation process for cart and checkout
     * Ignore Limit Selection validation and process magento validation without throwing error, because
     * SKU Policy independent/grouped may require to choose values for already excluded values-products
     *
     * @param ProductInterface $product
     * @param CustomOptionInterface $option
     * @return bool
     */
    public function canValidateCartCheckout($product, $option)
    {
        return true;
    }
}
