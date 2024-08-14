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

namespace Mageplaza\CallForPrice\Plugin\Customer;

use Magento\Customer\Model\Registration;
use Mageplaza\CallForPrice\Helper\Data as HelperData;

/**
 * Class DisableRegistration
 * @package Mageplaza\CallForPrice\Plugin\Customer
 */
class DisableRegistration
{
    /**
     * @var HelperData
     */
    protected $helperData;

    /**
     * DisableRegistration constructor.
     *
     * @param HelperData $helperData
     */
    public function __construct(HelperData $helperData)
    {
        $this->helperData = $helperData;
    }

    /**
     * @param Registration $subject
     * @param              $result
     *
     * @return bool
     */
    public function afterIsAllowed(Registration $subject, $result)
    {
        if (!$this->helperData->isEnabled()) {
            return $result;
        }

        return !$this->helperData->getDisableRegisterCustomerConfig();
    }
}
