<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Model\Rule\Condition;

use Magento\Rule\Model\Condition\Context;
use Aheadworks\OnSale\Model\Rule\Condition\Product\AttributesFactory;
use Aheadworks\OnSale\Model\Rule\Condition\Product\SpecialFactory;

/**
 * Class Combine
 *
 * @package Aheadworks\OnSale\Model\Rule\Condition
 */
class Combine extends \Magento\Rule\Model\Condition\Combine
{
    /**
     * @var AttributesFactory
     */
    private $attributeFactory;

    /**
     * @var SpecialFactory
     */
    protected $specialFactory;

    /**
     * @param Context $context
     * @param AttributesFactory $attributesFactory
     * @param SpecialFactory $specialFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        AttributesFactory $attributesFactory,
        SpecialFactory $specialFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->attributeFactory = $attributesFactory;
        $this->specialFactory = $specialFactory;
        $this->setType(Combine::class);
    }

    /**
     * Prepare child rules option list
     *
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        $conditions = [
            [
                'value' => $this->getType(),
                'label' => __('Conditions Combination')
            ],
            $this->attributeFactory->create()->getNewChildSelectOptions(),
            $this->specialFactory->create()->getNewChildSelectOptions(),
        ];

        $conditions = array_merge_recursive(parent::getNewChildSelectOptions(), $conditions);
        return $conditions;
    }

    /**
     * Return conditions
     *
     * @return array|mixed
     */
    public function getConditions()
    {
        if ($this->getData($this->getPrefix()) === null) {
            $this->setData($this->getPrefix(), []);
        }
        return $this->getData($this->getPrefix());
    }

    /**
     * Collect the valid attributes
     *
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $productCollection
     * @return $this
     */
    public function collectValidatedAttributes($productCollection)
    {
        foreach ($this->getConditions() as $condition) {
            $condition->collectValidatedAttributes($productCollection);
        }
        return $this;
    }
}
