<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Coupongenerator\Ui\Component\DataProvider\Rule;

use Aheadworks\Coupongenerator\Model\Source\Rule\Status as RuleStatusSource;
use Magento\Framework\Api\AttributeValueFactory;

/**
 * Class Document
 * @package Aheadworks\Coupongenerator\Ui\Component\DataProvider\Rule
 */
class Document extends \Magento\Framework\View\Element\UiComponent\DataProvider\Document
{
    /**
     * @var string
     */
    private static $statusAttributeCode = 'is_active';

    /**
     * @var RuleStatusSource
     */
    private $ruleStatusSource;

    /**
     * @param AttributeValueFactory $attributeValueFactory
     * @param RuleStatusSource $ruleStatusSource
     */
    public function __construct(
        AttributeValueFactory $attributeValueFactory,
        RuleStatusSource $ruleStatusSource
    ) {
        parent::__construct($attributeValueFactory);
        $this->ruleStatusSource = $ruleStatusSource;
    }

    /**
     * @inheritdoc
     */
    public function getCustomAttribute($attributeCode)
    {
        if ($attributeCode == self::$statusAttributeCode) {
            $value = $this->getData(self::$statusAttributeCode);
            $this->setCustomAttribute(self::$statusAttributeCode, $this->ruleStatusSource->getOptionByValue($value));
        }
        return parent::getCustomAttribute($attributeCode);
    }
}
