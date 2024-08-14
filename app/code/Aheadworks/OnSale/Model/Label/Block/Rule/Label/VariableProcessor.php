<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Model\Label\Block\Rule\Label;

use Magento\Catalog\Api\Data\ProductInterface;

/**
 * Class VariableProcessor
 *
 * @package Aheadworks\OnSale\Model\Label\Block\Rule\Label
 */
class VariableProcessor
{
    /**
     * @var array[]
     */
    private $processors;

    /**
     * @param array $processors
     */
    public function __construct(array $processors = [])
    {
        $this->processors = $processors;
    }

    /**
     * Process variable in label text
     *
     * @param ProductInterface $product
     * @param string $labelText
     * @return array
     */
    public function processVariableInLabelText($product, $labelText)
    {
        return $this->processLabelText($product, $labelText, false);
    }

    /**
     * Process variable in label text
     *
     * @param string $labelText
     * @return array
     */
    public function processVariableInLabelTestText($labelText)
    {
        return $this->processLabelText(null, $labelText, true);
    }

    /**
     * Process label text
     *
     * @param ProductInterface|null $product
     * @param string $labelText
     * @param bool $forTest
     * @return array
     */
    private function processLabelText($product, $labelText, $forTest)
    {
        $variables = $this->extractVariablesFromText($labelText);
        $variableValues = [];
        foreach ($variables as $variable) {
            $variableValue = '';
            list($variableName, $params) = $this->extractVariableNameAndParams($variable);
            if (isset($this->processors[$variableName])) {
                $variableValue = $forTest
                    ? $this->processors[$variableName]->processTest($params)
                    : $this->processors[$variableName]->process($product, $params);

                $variableValue = $this->convertValueToString($variableValue);
            }
            $variableValues[$variable] = $variableValue;
        }
        return $variableValues;
    }

    /**
     * Extract variables from label text
     *
     * @param string $labelText
     * @return array
     */
    private function extractVariablesFromText($labelText)
    {
        preg_match_all('/{(.*?)}/', $labelText, $variables);

        return $variables[1];
    }

    /**
     * Extract param from variable
     *
     * @param string $variable
     * @return array
     */
    private function extractVariableNameAndParams($variable)
    {
        $params = explode(':', $variable);
        $variableName = array_shift($params);

        return [strtolower($variableName), $params];
    }

    /**
     * Convert value to string
     *
     * @param mixed $value
     * @return string
     */
    private function convertValueToString($value)
    {
        if (is_array($value)) {
            $value = implode(' ', $value);
        }

        return $value;
    }
}
