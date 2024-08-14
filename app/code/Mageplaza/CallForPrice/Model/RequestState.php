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

namespace Mageplaza\CallForPrice\Model;

use Magento\Framework\Option\ArrayInterface;
use Mageplaza\CallForPrice\Helper\Data as HelperData;

/**
 * Class RequestState
 * @package Mageplaza\CallForPrice\Model
 */
class RequestState implements ArrayInterface
{
    const TODO = 'To Do';

    /**
     * @var HelperData
     */
    protected $_helperData;

    /**
     * RequestState constructor.
     *
     * @param HelperData $helperData
     */
    public function __construct(HelperData $helperData)
    {
        $this->_helperData = $helperData;
    }

    /**
     * @return array
     * @throws \Zend_Serializer_Exception
     */
    public function toOptionArray()
    {
        /** if admin use custom request config status*/
        $requestStateConfig = $this->_helperData->getRequestStatusConfig();
        $customStateArray   = [];
        $customOptions      = [];
        foreach ($requestStateConfig as $keyroof => $customState) {
            foreach ($customState as $key => $value) {
                if ($key == 'labelstatus') {
                    $customStateArray[$keyroof] = $value;
                }
            }
        }

        foreach ($customStateArray as $key => $value) {
            $customOptions[] = [
                'value' => $key,
                'label' => $value
            ];
        }

        return $customOptions;
    }
}
