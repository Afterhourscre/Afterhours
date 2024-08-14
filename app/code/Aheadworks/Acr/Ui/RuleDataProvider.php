<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Acr\Ui;

use Aheadworks\Acr\Api\RuleRepositoryInterface;
use Aheadworks\Acr\Api\Data\RuleInterface;
use Magento\Framework\Api\Filter;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;

/**
 * Class RuleDataProvider
 * @package Aheadworks\Acr\Ui
 */
class RuleDataProvider extends AbstractDataProvider
{
    /**
     * @var RuleRepositoryInterface
     */
    protected $ruleRepository;

    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @var DataObjectProcessor
     */
    private $dataObjectProcessor;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param RuleRepositoryInterface $ruleRepository
     * @param DataPersistorInterface $dataPersistor
     * @param DataObjectProcessor $dataObjectProcessor
     * @param RequestInterface $request
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        RuleRepositoryInterface $ruleRepository,
        DataPersistorInterface $dataPersistor,
        DataObjectProcessor $dataObjectProcessor,
        RequestInterface $request,
        array $meta = [],
        array $data = []
    ) {
        $this->ruleRepository = $ruleRepository;
        $this->dataPersistor = $dataPersistor;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->request = $request;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        $data = [];
        $dataFromForm = $this->dataPersistor->get('aw_acr_rule');
        if (!empty($dataFromForm)) {
            if (isset($dataFromForm['id'])) {
                $data[$dataFromForm['id']] = $dataFromForm;
            } else {
                $data[null] = $dataFromForm;
            }
            $this->dataPersistor->clear('aw_acr_rule');
        } else {
            $id = $this->request->getParam($this->getRequestFieldName());
            if ($id) {
                /** @var RuleInterface $ruleDataObject */
                $ruleDataObject = $this->ruleRepository->get($id);

                $formData = $this->dataObjectProcessor->buildOutputDataArray(
                    $ruleDataObject,
                    RuleInterface::class
                );

                $formData = $this->convertToString(
                    $formData,
                    [
                        RuleInterface::STATUS,
                        RuleInterface::STORE_IDS,
                        RuleInterface::EMAIL_SEND_DAYS,
                        RuleInterface::EMAIL_SEND_HOURS,
                        RuleInterface::EMAIL_SEND_MINUTES,
                        RuleInterface::CUSTOMER_GROUPS,
                        RuleInterface::COUPON_RULE
                    ]
                );
                $data[$ruleDataObject->getId()] = $formData;
            }
        }
        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function addFilter(Filter $filter)
    {
        return $this;
    }

    /**
     * Convert selected fields to string
     *
     * @param [] $data
     * @param string[] $fields
     * @return []
     */
    private function convertToString($data, $fields)
    {
        foreach ($fields as $field) {
            if (isset($data[$field])) {
                if (is_array($data[$field])) {
                    foreach ($data[$field] as $key => $value) {
                        if ($value === false) {
                            $data[$field][$key] = '0';
                        } else {
                            $data[$field][$key] = (string)$value;
                        }
                    }
                } else {
                    $data[$field] = (string)$data[$field];
                }
            }
        }
        return $data;
    }
}
