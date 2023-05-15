<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Ui\DataProvider\Rule;

use Aheadworks\OnSale\Model\ResourceModel\Rule\CollectionFactory;
use Aheadworks\OnSale\Model\ResourceModel\Rule\Collection;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Aheadworks\OnSale\Api\Data\RuleInterface;

/**
 * Class FormDataProvider
 *
 * @package Aheadworks\OnSale\Ui\DataProvider\Rule
 */
class FormDataProvider extends AbstractDataProvider
{
    /**
     * Key for saving and getting form data from data persistor
     */
    const DATA_PERSISTOR_FORM_DATA_KEY = 'aw_onsale_rule';

    /**
     * @var Collection
     */
    protected $collection;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param RequestInterface $request
     * @param DataPersistorInterface $dataPersistor
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        RequestInterface $request,
        DataPersistorInterface $dataPersistor,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $collectionFactory->create();
        $this->request = $request;
        $this->dataPersistor = $dataPersistor;
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        $preparedData = [];
        $dataFromForm = $this->dataPersistor->get(self::DATA_PERSISTOR_FORM_DATA_KEY);

        if (!empty($dataFromForm) && (is_array($dataFromForm)) && (!empty($dataFromForm[RuleInterface::RULE_ID]))) {
            $id = $dataFromForm[RuleInterface::RULE_ID];
            $this->dataPersistor->clear(self::DATA_PERSISTOR_FORM_DATA_KEY);
            $preparedData[$id] = $dataFromForm;
        } else {
            $id = $this->request->getParam($this->getRequestFieldName());
            $rules = $this->getCollection()->addFieldToFilter(RuleInterface::RULE_ID, $id)->getItems();
            /** @var RuleInterface $rule */
            foreach ($rules as $rule) {
                if ($id == $rule->getRuleId()) {
                    $preparedData[$id] = $this->getPreparedRuleData($rule->getData());
                }
            }
        }

        return $preparedData;
    }

    /**
     * Retrieve array with prepared rule data
     *
     * @param array $ruleData
     * @return array
     */
    private function getPreparedRuleData($ruleData)
    {
        return $ruleData;
    }
}
