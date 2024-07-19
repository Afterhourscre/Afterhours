<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */


namespace Aheadworks\Helpdesk\Ui\Component\MassAction\Ticket\Department;

use Magento\Framework\UrlInterface;
use JsonSerializable;
use Aheadworks\Helpdesk\Model\Source\Ticket\Department as TicketDepartmentSourceModel;

/**
 * Class Options
 * @package Aheadworks\Helpdesk\Ui\Component\MassAction\Ticket\Department
 */
class Options implements JsonSerializable
{
    /**
     * Options
     *
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
     * Agent source
     *
     * @var TicketDepartmentSourceModel
     */
    private $departmentSource;

    /**
     * @param UrlInterface $urlBuilder
     * @param TicketDepartmentSourceModel $departmentSource
     * @param array $data
     */
    public function __construct(
        UrlInterface $urlBuilder,
        TicketDepartmentSourceModel $departmentSource,
        array $data = []
    ) {
        $this->departmentSource = $departmentSource;
        $this->data = $data;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * Get action options
     *
     * @return array
     */
    public function jsonSerialize(): mixed
    {
        if ($this->options === null) {
            $options = $this->departmentSource->getAvailableOptionsForUpdate();
            $this->prepareData();
            foreach ($options as $key => $optionCode) {
                $this->options[$key] = [
                    'type' => 'department' . $key,
                    'label' => $optionCode,
                ];

                if ($this->urlPath && $this->paramName) {
                    $this->options[$key]['url'] = $this->urlBuilder->getUrl(
                        $this->urlPath,
                        [$this->paramName => $key]
                    );
                }

                $this->options[$key] = array_merge_recursive(
                    $this->options[$key],
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
