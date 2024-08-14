<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Acr\Model\Sample\Converter;

/**
 * Class Xml
 * @package Aheadworks\Acr\Model\Sample\Converter
 */
class Xml implements \Magento\Framework\Config\ConverterInterface
{
    /**
     * {@inheritdoc}
     */
    public function convert($source)
    {
        $output = [];
        if (!$source instanceof \DOMDocument) {
            return $output;
        }

        $events = $source->getElementsByTagName('rule');
        foreach ($events as $event) {
            $eventData = [];
            /** @var $event \DOMElement */
            foreach ($event->childNodes as $child) {
                if (!$child instanceof \DOMElement) {
                    continue;
                }
                /** @var $event \DOMElement */
                $eventData[$child->nodeName] = $child->nodeValue;
            }
            $output[] = $eventData;
        }
        return $output;
    }
}
