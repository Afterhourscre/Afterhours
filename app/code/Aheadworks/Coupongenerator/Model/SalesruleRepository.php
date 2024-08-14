<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Coupongenerator\Model;

use Aheadworks\Coupongenerator\Model\SalesruleRegistry;
use Aheadworks\Coupongenerator\Api\Data\SalesruleInterface;
use Aheadworks\Coupongenerator\Api\Data\SalesruleInterfaceFactory;
use Aheadworks\Coupongenerator\Model\ResourceModel\SalesruleFactory;
use Magento\Framework\EntityManager\EntityManager;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Exception\NoSuchEntityException;

class SalesruleRepository
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var \Magento\Framework\Api\DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var SalesruleInterfaceFactory
     */
    private $salesruleInterfaceFactory;

    /**
     * @var \Aheadworks\Coupongenerator\Model\ResourceModel\SalesruleFactory
     */
    private $salesruleResourceFactory;

    /**
     * @var \Aheadworks\Coupongenerator\Model\SalesruleRegistry
     */
    private $salesruleRegistry;

    /**
     * @param EntityManager $entityManager
     * @param DataObjectHelper $dataObjectHelper
     * @param SalesruleInterfaceFactory $salesruleInterfaceFactory
     * @param SalesruleFactory $salesruleResourceFactory
     * @param \Aheadworks\Coupongenerator\Model\SalesruleRegistry $salesruleRegistry
     */
    public function __construct(
        EntityManager $entityManager,
        DataObjectHelper $dataObjectHelper,
        SalesruleInterfaceFactory $salesruleInterfaceFactory,
        SalesruleFactory $salesruleResourceFactory,
        SalesruleRegistry $salesruleRegistry
    ) {
        $this->entityManager = $entityManager;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->salesruleInterfaceFactory = $salesruleInterfaceFactory;
        $this->salesruleResourceFactory = $salesruleResourceFactory;
        $this->salesruleRegistry = $salesruleRegistry;
    }

    /**
     * Get salesrule data model by salesrule id
     *
     * @param int $salesruleId
     * @return SalesruleInterface
     * @throws NoSuchEntityException
     */
    public function get($salesruleId)
    {
        /** @var \Aheadworks\Coupongenerator\Api\Data\SalesruleInterface $salesruleDataObject */
        $salesruleDataObject = $this->salesruleRegistry->retrieve($salesruleId);

        if ($salesruleDataObject === null) {
            /** @var \Aheadworks\Coupongenerator\Api\Data\SalesruleInterface $salesruleDataObject */
            $salesruleDataObject = $this->salesruleInterfaceFactory->create();
            $this->entityManager->load($salesruleDataObject, $salesruleId);

            if (!$salesruleDataObject->getId()) {
                throw NoSuchEntityException::singleField('id', $salesruleId);
            } else {
                $this->salesruleRegistry->push($salesruleDataObject);
            }
        }

        return $salesruleDataObject;
    }

    /**
     * Get salesrule data model by magento rule id
     *
     * @param int $ruleId
     * @return SalesruleInterface
     * @throws NoSuchEntityException
     */
    public function getByRuleId($ruleId)
    {
        /** @var \Aheadworks\Coupongenerator\Api\Data\SalesruleInterface $salesruleDataObject */
        $salesruleDataObject = $this->salesruleRegistry->retrieveByRuleId($ruleId);

        if ($salesruleDataObject === null) {
            /** @var \Aheadworks\Coupongenerator\Model\ResourceModel\Salesrule $salesruleResource */
            $salesruleResource = $this->salesruleResourceFactory->create();
            $salesruleData = $salesruleResource->getByRuleId($ruleId);

            if (!$salesruleData) {
                throw NoSuchEntityException::singleField('rule_id', $ruleId);
            } else {
                $salesruleDataObject = $this->salesruleInterfaceFactory->create();

                $this->dataObjectHelper->populateWithArray(
                    $salesruleDataObject,
                    $salesruleData,
                    SalesruleInterface::class
                );

                $this->salesruleRegistry->push($salesruleDataObject);
            }
        }

        return $salesruleDataObject;
    }

    /**
     * Delete salesrule data model
     *
     * @param SalesruleInterface $salesruleDataObject
     * @return bool
     */
    public function delete(SalesruleInterface $salesruleDataObject)
    {
        return $this->deleteById($salesruleDataObject->getId());
    }

    /**
     * Delete salesrule data model by salesrule id
     *
     * @param int $salesruleId
     * @return bool
     */
    public function deleteById($salesruleId)
    {
        /** @var \Aheadworks\Coupongenerator\Api\Data\SalesruleInterface $salesruleDataObject */
        $salesruleDataObject = $this->salesruleRegistry->retrieve($salesruleId);

        if ($salesruleDataObject === null) {
            /** @var \Aheadworks\Coupongenerator\Api\Data\SalesruleInterface $salesruleDataObject */
            $salesruleDataObject = $this->salesruleInterfaceFactory->create();
            $this->entityManager->load($salesruleDataObject, $salesruleId);
        }

        if ($salesruleDataObject->getId()) {
            $this->entityManager->delete($salesruleDataObject);
        }
        $this->salesruleRegistry->remove($salesruleId);

        return true;
    }

    public function deleteByRuleId($ruleId)
    {
        /** @var \Aheadworks\Coupongenerator\Api\Data\SalesruleInterface $salesruleDataObject */
        $salesruleDataObject = $this->salesruleRegistry->retrieveByRuleId($ruleId);

        if ($salesruleDataObject === null) {
            /** @var \Aheadworks\Coupongenerator\Model\ResourceModel\Salesrule $salesruleResource */
            $salesruleResource = $this->salesruleResourceFactory->create();
            $salesruleData = $salesruleResource->getByRuleId($ruleId);
            if ($salesruleData) {
                $salesruleDataObject = $this->salesruleInterfaceFactory->create();

                $this->dataObjectHelper->populateWithArray(
                    $salesruleDataObject,
                    $salesruleData,
                    SalesruleInterface::class
                );
            }
        }

        if ($salesruleDataObject != null && $salesruleDataObject->getId()) {
            $this->entityManager->delete($salesruleDataObject);
        }

        $this->salesruleRegistry->removeByRuleId($ruleId);

        return true;
    }

    /**
     * Save salesrule data model
     *
     * @param SalesruleInterface $salesruleDataObject
     * @return SalesruleInterface
     */
    public function save(SalesruleInterface $salesruleDataObject)
    {
        $salesruleDataObject = $this->entityManager->save($salesruleDataObject);
        $this->salesruleRegistry->push($salesruleDataObject);

        return $salesruleDataObject;
    }
}
