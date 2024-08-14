<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Acr\Setup;

use Aheadworks\Acr\Api\Data\RuleInterface;
use Aheadworks\Acr\Api\Data\RuleInterfaceFactory;
use Aheadworks\Acr\Api\RuleRepositoryInterface;
use Aheadworks\Acr\Model\Source\Rule\Status as RuleStatus;
use Aheadworks\Acr\Model\Sample;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * Class InstallData
 * @package Aheadworks\Acr\Setup
 */
class InstallData implements InstallDataInterface
{
    /**
     * @var RuleInterfaceFactory
     */
    private $ruleFactory;

    /**
     * @var RuleRepositoryInterface
     */
    private $ruleRepository;

    /**
     * @var Sample
     */
    private $sampleData;

    /**
     * @param RuleInterfaceFactory $ruleFactory
     * @param RuleRepositoryInterface $ruleRepository
     * @param Sample $sampleData
     */
    public function __construct(
        RuleInterfaceFactory $ruleFactory,
        RuleRepositoryInterface $ruleRepository,
        Sample $sampleData
    ) {
        $this->ruleFactory = $ruleFactory;
        $this->ruleRepository = $ruleRepository;
        $this->sampleData = $sampleData;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        foreach ($this->sampleData->get() as $data) {
            try {
                /** @var RuleInterface|AbstractModel $rule */
                $rule = $this->ruleFactory->create();
                $rule
                    ->setData($data)
                    ->setStatus(RuleStatus::DISABLED)
                    ->setStoreIds([0])
                    ->setProductTypeIds(['all'])
                    ->setCustomerGroups(['all'])
                    ->setProductConditions(serialize([]))
                    ->setCartConditions(serialize([]))
                ;
                $this->ruleRepository->save($rule);
            } catch (\Exception $e) {
                // do nothing
            }
        }

        $setup->endSetup();
    }
}
