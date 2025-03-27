<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Ui\Component\MassAction\Rule\Label;

use Magento\Framework\UrlInterface;
use Laminas\Stdlib\JsonSerializable;
use Aheadworks\OnSale\Api\LabelRepositoryInterface;
use Magento\Framework\Convert\DataObject;
use Aheadworks\OnSale\Api\Data\LabelInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;

/**
 * Class Options
 *
 * @package Aheadworks\OnSale\Ui\Component\MassAction\Rule\Label
 */
class Options implements JsonSerializable
{
    /**
     * @var array
     */
    private $options;

    /**
     * Additional options params
     *
     * @var array
     */
    private $data;

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * Base URL for subactions
     *
     * @var string
     */
    private $urlPath;

    /**
     * Param name for subactions
     *
     * @var string
     */
    private $paramName;

    /**
     * Additional params for subactions
     *
     * @var array
     */
    private $additionalData = [];

    /**
     * @var LabelRepositoryInterface
     */
    private $labelRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var DataObject
     */
    private $objectConverter;

    /**
     * @param UrlInterface $urlBuilder
     * @param LabelRepositoryInterface $labelRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param DataObject $objectConverter
     * @param array $data
     */
    public function __construct(
        UrlInterface $urlBuilder,
        LabelRepositoryInterface $labelRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        DataObject $objectConverter,
        array $data = []
    ) {
        $this->data = $data;
        $this->urlBuilder = $urlBuilder;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->labelRepository = $labelRepository;
        $this->objectConverter = $objectConverter;
    }

    /**
     * Get action options
     *
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function jsonSerialize(): mixed
    {
        if ($this->options === null) {
            $labels = $this->labelRepository->getList($this->searchCriteriaBuilder->create())->getItems();
            $options = $this->objectConverter->toOptionArray($labels, LabelInterface::LABEL_ID, LabelInterface::NAME);
            $this->prepareData();
            $optionsData = [];
            foreach ($options as $optionCode) {
                $optionsData[$optionCode['value']] = [
                    'type' => $this->data['type'] . $optionCode['value'],
                    'label' => $optionCode['label'],
                ];

                if ($this->urlPath && $this->paramName) {
                    $optionsData[$optionCode['value']]['url'] = $this->urlBuilder->getUrl(
                        $this->urlPath,
                        [$this->paramName => $optionCode['value']]
                    );
                }

                $optionsData[$optionCode['value']] = array_merge_recursive(
                    $optionsData[$optionCode['value']],
                    $this->additionalData
                );
            }
            if ($optionsData) {
                $this->options = array_values($optionsData);
            }
        }

        return $this->options;
    }

    /**
     * Prepare addition data for subactions
     *
     * @return void
     */
    protected function prepareData()
    {
        foreach ($this->data as $key => $value) {
            switch ($key) {
                case 'urlPath':
                    $this->urlPath = $value;
                    break;
                case 'paramName':
                    $this->paramName = $value;
                    break;
                default:
                    $this->additionalData[$key] = $value;
                    break;
            }
        }
    }
}
