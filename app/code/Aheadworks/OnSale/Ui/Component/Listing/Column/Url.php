<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Ui\Component\Listing\Column;

use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Class Url
 *
 * @package Aheadworks\OnSale\Ui\Component\Listing\Column
 */
class Url extends Column
{
    /**#@+
     * Constants defined for getting params from config
     */
    const CONFIG_URL_ROUTE = 'config/url_route';
    const CONFIG_URL_PARAMS = 'config/url_params';
    const CONFIG_URL_PARAM_KEY = 'url_param_key';
    const CONFIG_URL_PARAM_VALUE_FIELD = 'url_param_value_field';
    const CONFIG_CUSTOM_ENTITY_LABEL_KEY = 'custom_entity_label_key';
    /**#@-*/

    /**
     * {@inheritdoc}
     */
    public function prepareDataSource(array $dataSource)
    {
        $fieldName = $this->getFieldName();
        foreach ($dataSource['data']['items'] as & $item) {
            $item[$fieldName . '_label'] = $this->extractEntityLabel($item);
            if ($this->isAvailableUrl($item)) {
                $item[$fieldName . '_url'] = $this->getUrl($item);
            }
        }

        return $dataSource;
    }

    /**
     * Check if available url field
     *
     * @param array $item
     * @return bool
     */
    private function isAvailableUrl($item)
    {
        return !empty($this->getPreparedUrlParams($item));
    }

    /**
     * Retrieve current field name
     *
     * @return string
     */
    private function getFieldName()
    {
        return $this->getData('name');
    }

    /**
     * Generate url for specified item
     *
     * @param array $item
     * @return string
     */
    private function getUrl($item)
    {
        return $this->context->getUrl(
            $this->getRoute(),
            $this->getPreparedUrlParams($item)
        );
    }

    /**
     * Retrieve route for url generation
     *
     * @return string
     */
    private function getRoute()
    {
        return $this->getData(self::CONFIG_URL_ROUTE);
    }

    /**
     * Extract entity label from item data array
     *
     * @param array $item
     * @return string
     */
    private function extractEntityLabel($item)
    {
        $config = $this->getData('config');
        return isset($config[self::CONFIG_CUSTOM_ENTITY_LABEL_KEY])
            ? $item[$config[self::CONFIG_CUSTOM_ENTITY_LABEL_KEY]]
            : $item[$this->getFieldName()];
    }

    /**
     * Retrieve url params based on component config
     *
     * @param array $item
     * @return array
     */
    private function getPreparedUrlParams($item)
    {
        $urlParams = [];
        $urlParamsConfig = $this->getData(self::CONFIG_URL_PARAMS);
        if (is_array($urlParamsConfig)) {
            foreach ($urlParamsConfig as $urlParamConfigItem) {
                if ($this->isUrlParamConfigItemValid($urlParamConfigItem)) {
                    $urlParamValue = $item[$urlParamConfigItem[self::CONFIG_URL_PARAM_VALUE_FIELD]];
                    if (!empty($urlParamValue)) {
                        $urlParams[$urlParamConfigItem[self::CONFIG_URL_PARAM_KEY]] = $urlParamValue;
                    }
                }
            }
        }
        return $urlParams;
    }

    /**
     * Check if url param config valid
     *
     * @param array $urlParamConfigItem
     * @return bool
     */
    private function isUrlParamConfigItemValid($urlParamConfigItem)
    {
        return (is_array($urlParamConfigItem)
            && isset($urlParamConfigItem[self::CONFIG_URL_PARAM_KEY])
            && isset($urlParamConfigItem[self::CONFIG_URL_PARAM_VALUE_FIELD])
        );
    }
}
