<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Helpdesk\Ui\Component\MassAction;

use Magento\Framework\UrlInterface;
use Zend\Stdlib\JsonSerializable;
use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Options
 *
 * @package Aheadworks\Helpdesk\Ui\Component\MassAction
 */
class Options implements JsonSerializable
{
    /**
     * Options
     * @var array
     */
    protected $options;

    /**
     * Additional options params
     *
     * @var array
     */
    protected $data;

    /**
     * Url builder
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * Base URL for subactions
     *
     * @var string
     */
    protected $urlPath;

    /**
     * Param name for subactions
     *
     * @var string
     */
    protected $paramName;

    /**
     * Additional params for subactions
     *
     * @var array
     */
    protected $additionalData = [];

    /**
     * Status source
     *
     * @var OptionSourceInterface
     */
    protected $optionSource;

    /**
     * Constructor
     *
     * @param UrlInterface $urlBuilder
     * @param OptionSourceInterface $optionSource
     * @param array $data
     */
    public function __construct(
        UrlInterface $urlBuilder,
        OptionSourceInterface $optionSource,
        array $data = []
    ) {
        $this->optionSource = $optionSource;
        $this->data = $data;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * Get action options
     *
     * @return array
     */
    public function jsonSerialize()
    {
        if ($this->options === null) {
            $this->prepareData();
            $options = $this->optionSource->toOptionArray();
            foreach ($options as $optionCode) {
                $this->options[$optionCode['value']] = [
                    'type' => (empty($this->paramName) ? 'type' : $this->paramName) . '_' . $optionCode['value'],
                    'label' => $optionCode['label'],
                ];

                if ($this->urlPath && $this->paramName) {
                    $this->options[$optionCode['value']]['url'] = $this->urlBuilder->getUrl(
                        $this->urlPath,
                        [$this->paramName => $optionCode['value']]
                    );
                }

                $this->options[$optionCode['value']] = array_merge_recursive(
                    $this->options[$optionCode['value']],
                    $this->additionalData
                );
            }

            $this->options = array_values($this->options);
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
