<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Coupongenerator\Model\Plugin;

/**
 * Class SalesruleCollection
 * @package Aheadworks\Coupongenerator\Model\Plugin
 * @codeCoverageIgnore
 */
class SalesruleCollection
{
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private $request;

    /**
     * @param \Magento\Framework\App\RequestInterface $request
     */
    public function __construct(
        \Magento\Framework\App\RequestInterface $request
    ) {
        $this->request = $request;
    }

    /**
     * @param $type
     * @param $result
     * @return mixed
     */
    // @codingStandardsIgnoreStart
    public function after_initSelect($type, $result)
    {
        if ($this->request->getModuleName() == 'sales_rule'
            && $this->request->getControllerName() == 'promo_quote'
            && $this->request->getActionName() == 'index'
        ) {
            $result
                ->getSelect()
                ->joinLeft(
                    ["awsr" => $type->getTable('aw_coupongenerator_salesrule')],
                    "awsr.rule_id = main_table.rule_id",
                    ["awsr.id"]
                )
                ->where("awsr.id IS NULL")
            ;
        }

        return $result;
    }
    // @codingStandardsIgnoreEnd
}
