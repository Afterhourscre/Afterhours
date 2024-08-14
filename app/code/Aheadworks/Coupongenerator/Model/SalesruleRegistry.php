<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Coupongenerator\Model;

use Aheadworks\Coupongenerator\Api\Data\SalesruleInterface;

/**
 * Class SalesruleRegistry
 * @package Aheadworks\Coupongenerator\Model
 */
class SalesruleRegistry
{
    /**
     * @var array
     */
    private $salesruleRegistryById = [];

    /**
     * @var array
     */
    private $salesruleRegistryByRuleId = [];

    /**
     * Retrieve salesrule data model by salesrule id
     *
     * @param int $salesruleId
     * @return \Aheadworks\Coupongenerator\Api\Data\SalesruleInterface|null
     */
    public function retrieve($salesruleId)
    {
        if (isset($this->salesruleRegistryById[$salesruleId])) {
            return $this->salesruleRegistryById[$salesruleId];
        }

        return null;
    }

    /**
     * Retrieve salesrule data model by magento rule id
     *
     * @param int $ruleId
     * @return SalesruleInterface|null
     */
    public function retrieveByRuleId($ruleId)
    {
        if (isset($this->salesruleRegistryByRuleId[$ruleId])) {
            return $this->salesruleRegistryByRuleId[$ruleId];
        }

        return null;
    }

    /**
     * Remove salesrule data model from registry by salesrule id
     *
     * @param int $salesruleId
     * @return void
     */
    public function remove($salesruleId)
    {
        if (isset($this->salesruleRegistryById[$salesruleId])) {
            /** @var \Aheadworks\Coupongenerator\Api\Data\SalesruleInterface $salesruleDataObject */
            $salesruleDataObject = $this->salesruleRegistryById[$salesruleId];
            unset($this->salesruleRegistryById[$salesruleId]);
            unset($this->salesruleRegistryByRuleId[$salesruleDataObject->getRuleId()]);
        }
    }

    /**
     * Remove salesrule data model from registry by magento rule id
     *
     * @param int $ruleId
     * @return void
     */
    public function removeByRuleId($ruleId)
    {
        if (isset($this->salesruleRegistryByRuleId[$ruleId])) {
            /** @var \Aheadworks\Coupongenerator\Api\Data\SalesruleInterface $salesruleDataObject */
            $salesruleDataObject = $this->salesruleRegistryByRuleId[$ruleId];
            unset($this->salesruleRegistryById[$salesruleDataObject->getId()]);
            unset($this->salesruleRegistryByRuleId[$ruleId]);
        }
    }

    /**
     * Replace existing salesrule data model with a new one
     *
     * @param SalesruleInterface $salesruleDataObject
     * @return $this
     */
    public function push(SalesruleInterface $salesruleDataObject)
    {
        if ($salesruleDataObject->getId()) {
            $this->salesruleRegistryById[$salesruleDataObject->getId()] = $salesruleDataObject;
            $this->salesruleRegistryByRuleId[$salesruleDataObject->getRuleId()] = $salesruleDataObject;
        }

        return $this;
    }
}
