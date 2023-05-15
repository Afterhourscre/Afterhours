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

namespace Mageplaza\Barclaycard\Block;

use Magento\Framework\DataObject;
use Magento\Framework\Phrase;
use Magento\Framework\View\Element\Template\Context;
use Magento\Payment\Block\ConfigurableInfo;
use Magento\Payment\Gateway\ConfigInterface;
use Mageplaza\Barclaycard\Helper\Data;
use Mageplaza\Barclaycard\Model\Source\PaymentInfo;

/**
 * Class Info
 * @package Mageplaza\Barclaycard\Block
 */
class Info extends ConfigurableInfo
{
    /**
     * @var Data
     */
    private $helper;

    /**
     * Info constructor.
     *
     * @param Context $context
     * @param ConfigInterface $config
     * @param Data $helper
     * @param array $data
     */
    public function __construct(
        Context $context,
        ConfigInterface $config,
        Data $helper,
        array $data = []
    ) {
        $this->helper = $helper;

        parent::__construct($context, $config, $data);
    }

    /**
     * @param string $field
     *
     * @return string|Phrase
     */
    protected function getLabel($field)
    {
        return PaymentInfo::getOptionArray()[$field];
    }

    /**
     * Sets data to transport
     *
     * @param DataObject $transport
     * @param string $field
     * @param string $value
     *
     * @return void
     */
    protected function setDataToTransfer(DataObject $transport, $field, $value)
    {
        if ($this->helper->isAdmin() || isset(PaymentInfo::getOptionArray(false)[$field])) {
            parent::setDataToTransfer($transport, $field, $value);
        }
    }
}
