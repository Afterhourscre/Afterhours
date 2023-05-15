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
 * @package     Mageplaza_Barclaycard
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\Barclaycard\Model\Source;

/**
 * Class HashAlgorithm
 * @package Mageplaza\Barclaycard\Model\Source
 */
class HashAlgorithm extends AbstractSource
{
    const SHA1   = 'sha1';
    const SHA256 = 'sha256';
    const SHA512 = 'sha512';

    /**
     * @return array
     */
    public static function getOptionArray()
    {
        return [
            self::SHA1   => __('SHA-1'),
            self::SHA256 => __('SHA-256'),
            self::SHA512 => __('SHA-512'),
        ];
    }
}
