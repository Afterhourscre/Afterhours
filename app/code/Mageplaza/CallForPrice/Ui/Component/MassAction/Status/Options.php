<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_CallForPrice
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\CallForPrice\Ui\Component\MassAction\Status;

use Magento\Framework\UrlInterface;
use Mageplaza\CallForPrice\Model\RequestState;
use Laminas\Stdlib\JsonSerializable;

/**
 * Class Options
 * @package Mageplaza\CallForPrice\Ui\Component\MassAction\Status
 */
class Options implements JsonSerializable
{
    /**
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
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var RequestState
     */
    protected $requeststate;

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
     * Options constructor.
     *
     * @param UrlInterface $urlBuilder
     * @param RequestState $requeststate
     * @param array $data
     */
    public function __construct(
        UrlInterface $urlBuilder,
        RequestState $requeststate,
        array $data = []
    )
    {
        $this->data         = $data;
        $this->urlBuilder   = $urlBuilder;
        $this->requeststate = $requeststate;
    }

    /**
     * @return array|mixed
     * @throws \Zend_Serializer_Exception
     */
    public function jsonSerialize(): mixed
    {
        
die('123');
        if ($this->options === null) {
          
            $options = $this->requeststate->toOptionArray();
            $this->prepareData();

            foreach ($options as $optionCode) {
                $this->options[$optionCode['value']] = [
                    'type'  => $optionCode['value'],
                    'label' => $optionCode['label']
                    // '__disableTmpl' => true
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
