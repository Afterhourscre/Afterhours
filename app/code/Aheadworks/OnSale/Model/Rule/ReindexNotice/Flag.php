<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Model\Rule\ReindexNotice;

use Magento\Framework\Flag as FrameworkFlag;

/**
 * Class Flag
 * It indicates that some rules are changed but changes have not been applied yet.
 *
 * @package Magento\OnSale\Model\Rule
 */
class Flag extends FrameworkFlag
{
    /**
     * Flag code
     *
     * @var string
     */
    protected $_flagCode = 'aw_onsale_apply_rule_warning';
}
