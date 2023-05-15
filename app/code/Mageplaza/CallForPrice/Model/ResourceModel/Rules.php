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
 * @package     Mageplaza_CallForPrice
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\CallForPrice\Model\ResourceModel;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Rule\Model\ResourceModel\AbstractResource;
use Mageplaza\CallForPrice\Model\QuoteFields;

/**
 * Class Rules
 * @package Mageplaza\CallForPrice\Model\ResourceModel
 */
class Rules extends AbstractResource
{
    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_date;

    /**
     * Construct
     *
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param string $connectionName
     */
    public function __construct(
        Context $context,
        DateTime $date,
        $connectionName = null
    )
    {
        $this->_date = $date;

        parent::__construct($context, $connectionName);
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('mageplaza_callforprice_rules', 'rule_id');
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $object
     *
     * @return $this
     */
    public function _beforeSave(AbstractModel $object)
    {
        if (!is_array($object->getShowFields())) {
            $object->setShowFields(implode(',', [QuoteFields::EMAIL, QuoteFields::NOTE]));
        }

        if (!is_array($object->getRequiredFields())) {
            $object->setRequiredFields(implode(',', [QuoteFields::EMAIL, QuoteFields::NOTE]));
        }

        if (is_array($object->getStoreIds())) {
            $object->setStoreIds(implode(',', $object->getStoreIds()));
        }

        if (is_array($object->getCustomerGroupIds())) {
            $object->setCustomerGroupIds(implode(',', $object->getCustomerGroupIds()));
        }

        if (is_array($object->getShowFields())) {
            $object->setShowFields(implode(',', $object->getShowFields()));
        }

        if (is_array($object->getRequiredFields())) {
            $object->setRequiredFields(implode(',', $object->getRequiredFields()));
        }

        if (!$object->getCreatedAt()) {
            $object->setCreatedAt($this->_date->gmtDate());
        }

        return $this;
    }
}
