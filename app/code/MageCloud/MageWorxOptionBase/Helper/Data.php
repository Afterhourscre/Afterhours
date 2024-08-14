<?php
/**
 * Created by PhpStorm.
 * User: michael
 * Date: 11.02.19
 * Time: 12:55
 */

namespace MageCloud\MageWorxOptionBase\Helper;


class Data extends \MageWorx\OptionBase\Helper\Data
{
    /**
     * Search element of array by key and value
     *
     * @param string $key
     * @param string $value
     * @param array $array
     * @return string|null
     */
    public function searchArray($key, $value, $array)
    {
        foreach ($array as $k => $v) {
            if ($v[$key] == $value) {
                return $k;
            }
        }

        return null;
    }
}