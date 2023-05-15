<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Model\Label;

use Aheadworks\OnSale\Api\BlockRepositoryInterface;
use Aheadworks\OnSale\Model\Label\Block\Factory as BlockFactory;
use Aheadworks\OnSale\Api\Data\LabelInterface;
use Aheadworks\OnSale\Model\Label\Block\Rule\Counter;
use Aheadworks\OnSale\Model\ResourceModel\Rule\Indexer\RuleProduct\RuleProductInterface;
use Aheadworks\OnSale\Model\Label\Block\Rule\Loader;

/**
 * Class BlockRepository
 *
 * @package Aheadworks\OnSale\Model\Label
 */
class BlockRepository implements BlockRepositoryInterface
{
    /**
     * @var BlockFactory
     */
    private $blockFactory;

    /**
     * @var Loader
     */
    private $loader;

    /**
     * @var Counter
     */
    private $counter;

    /**
     * @param BlockFactory $blockFactory
     * @param Loader $loader
     * @param Counter $counter
     */
    public function __construct(
        BlockFactory $blockFactory,
        Loader $loader,
        Counter $counter
    ) {
        $this->blockFactory = $blockFactory;
        $this->loader = $loader;
        $this->counter = $counter;
    }

    /**
     * {@inheritdoc}
     */
    public function getList($product, $customerGroupId)
    {
        $blockItems = [];
        $storeId = $product->getStoreId();
        $availableRules = $this->loader->getAvailableRulesForProduct($product, $customerGroupId);
        $labels = $this->loader->getLabelsForRules($availableRules);
        $this->counter->reset();

        foreach ($availableRules as $availableRule) {
            $label = $this->findLabelById($labels, $availableRule[RuleProductInterface::LABEL_ID]);
            if (!$this->counter->isLimitReached($label->getPosition(), $storeId)) {
                $labelTexts = [
                    RuleProductInterface::LABEL_TEXT_LARGE => $availableRule[RuleProductInterface::LABEL_TEXT_LARGE],
                    RuleProductInterface::LABEL_TEXT_MEDIUM => $availableRule[RuleProductInterface::LABEL_TEXT_MEDIUM],
                    RuleProductInterface::LABEL_TEXT_SMALL => $availableRule[RuleProductInterface::LABEL_TEXT_SMALL]
                ];

                $blockItems[] = $this->blockFactory->create($label, $labelTexts, $product);
            }
        }

        return $blockItems;
    }

    /**
     * Find label by id
     *
     * @param LabelInterface[] $labels
     * @param int $labelId
     * @return LabelInterface|null
     */
    private function findLabelById($labels, $labelId)
    {
        $foundLabel = null;
        foreach ($labels as $label) {
            if ($labelId == $label->getLabelId()) {
                $foundLabel = $label;
            }
        }
        return $foundLabel;
    }
}
