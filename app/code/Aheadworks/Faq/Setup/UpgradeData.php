<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Faq\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;

/**
 * Class UpgradeData
 * @package Aheadworks\Faq\Setup
 */
class UpgradeData implements UpgradeDataInterface
{
    /**
     * {@inheritdoc}
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.1.0', '<')) {
            $this->fillHelpfulnessRating($setup);
        }

        $setup->endSetup();
    }

    /**
     * Fill helpfulness rating for articles
     *
     * @param ModuleDataSetupInterface $setup
     */
    private function fillHelpfulnessRating(ModuleDataSetupInterface $setup)
    {
        /** @var AdapterInterface $connection */
        $connection = $setup->getConnection();
        $connection->update(
            $setup->getTable('aw_faq_article'),
            ['helpfulness_rating' => new \Zend_Db_Expr('CEIL(votes_yes / (votes_yes + votes_no) * 100)')],
            'votes_yes <> 0 OR votes_no <> 0'
        );
    }
}
