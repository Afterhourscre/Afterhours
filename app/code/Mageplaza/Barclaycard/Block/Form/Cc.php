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

namespace Mageplaza\Barclaycard\Block\Form;

/**
 * Class Cc
 * @package Mageplaza\Barclaycard\Block\Form
 */
class Cc extends \Magento\Payment\Block\Form\Cc
{
    /**
     * Retrieve field value data from payment info object
     *
     * @param string $field
     *
     * @return mixed
     */
    public function getInfoData($field)
    {
        if ($field === 'cc_type') {
            $ccTypes = array_keys($this->getCcAvailableTypes());

            return $ccTypes[0] ?? '';
        }

        return parent::getInfoData($field);
    }
}
