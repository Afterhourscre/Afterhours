<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Model\ResourceModel\Rule\Indexer\RuleProduct;

/**
 * Interface RuleIndexerDataInterface
 *
 * @package Aheadworks\OnSale\Model\ResourceModel\Rule\Indexer\RuleProduct
 */
interface RuleProductInterface
{
    /**#@+
     * Constants for keys of indexer fields.
     */
    const RULE_PRODUCT_ID = 'rule_product_id';
    const RULE_ID = 'rule_id';
    const FROM_DATE = 'from_date';
    const TO_DATE = 'to_date';
    const CUSTOMER_GROUP_ID = 'customer_group_id';
    const PRODUCT_ID = 'product_id';
    const PRIORITY = 'priority';
    const STORE_ID = 'store_id';
    const LABEL_ID = 'label_id';
    const LABEL_TEXT = 'label_text';
    const LABEL_TEXT_LARGE = 'label_text_large';
    const LABEL_TEXT_MEDIUM = 'label_text_medium';
    const LABEL_TEXT_SMALL = 'label_text_small';
    /**#@-*/
}
