<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the mageplaza.com license that is
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

namespace Mageplaza\CallForPrice\Block\Report;

use Magento\Backend\Block\Template;
use Mageplaza\CallForPrice\Helper\Data;

/**
 * Class AbstractReport
 * @package Mageplaza\Reports\Block\Dashboard
 */
abstract class AbstractReport extends Template
{
    const NAME              = '';
    const MAGE_REPORT_CLASS = '';

    /**
     * @var Data
     */
    protected $_helperData;

    /**
     * AbstractReport constructor.
     *
     * @param Template\Context $context
     * @param Data $helperData
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Data $helperData,
        array $data = []
    )
    {
        parent::__construct($context, $data);

        $this->_helperData = $helperData;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return static::NAME;
    }

    /**
     * @return bool
     */
    public function canShowDetail()
    {
        return false;
    }

    /**
     * @inheritdoc
     */
    public function getContentHtml()
    {
        if (static::MAGE_REPORT_CLASS) {
            return $this->getLayout()->createBlock(static::MAGE_REPORT_CLASS)->toHtml();
        }

        return $this->toHtml();
    }
}