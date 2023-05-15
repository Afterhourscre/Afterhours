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
 * @copyright   Copyright (c) Mageplaza (http://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\Barclaycard\Gateway\Config;

/**
 * Class Hosted
 * @package Mageplaza\Barclaycard\Gateway\Config
 */
class Hosted extends AbstractConfig
{
    /**
     * @return string
     */
    public function getShaOut()
    {
        return $this->encryptor->decrypt($this->getValue('sha_out'));
    }

    /**
     * @return string
     */
    public function getLangCode()
    {
        return $this->getValue('lang_code');
    }
}
