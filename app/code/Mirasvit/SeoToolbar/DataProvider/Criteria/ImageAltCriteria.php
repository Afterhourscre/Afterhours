<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-seo
 * @version   2.0.169
 * @copyright Copyright (C) 2020 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\SeoToolbar\DataProvider\Criteria;

use Mirasvit\SeoToolbar\Api\Data\DataProviderItemInterface;

class ImageAltCriteria extends AbstractCriteria
{
    const LABEL = 'Images';

    public function handle($content)
    {
        $images = $this->getImages($content);

        $emptyAlt = [];
        foreach ($images as $img => $alt) {
            if (trim($alt) == '') {
                $emptyAlt[] = $img;
            }
        }

        if (count($emptyAlt)) {
            return $this->getItem(
                self::LABEL,
                DataProviderItemInterface::STATUS_WARNING,
                __('%1 image(s) without ALT tag', count($emptyAlt)),
                implode(PHP_EOL, $emptyAlt)
            );
        }

        return $this->getItem(
            self::LABEL,
            DataProviderItemInterface::STATUS_NONE,
            __('%1 images with correct alt attribute.', count($images)),
            null
        );
    }

    /**
     * @param string $content
     * @return array
     */
    private function getImages($content)
    {
        $result = [];

        $matches = [];
        preg_match_all('/<img[^>]+>/i', $content, $matches);

        if (isset($matches[0])) {
            $img = [];

            foreach ($matches[0] as $imgTag) {
                preg_match_all('/(alt|src)="([^"]*)"/i', $imgTag, $img);

                if ($img) {
                    $src = isset($img[2][0]) ? $img[2][0] : '';
                    $alt = isset($img[2][1]) ? $img[2][1] : '';

                    if ($src) {
                        $result[$src] = $alt;
                    }
                }
            }
        }

        return $result;
    }
}