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
 * @package     Mageplaza_Worldpay
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\Worldpay\Block\Adminhtml\Backend;

use Exception;
use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use Magento\Config\Model\Config\Source\Locale\Currency;
use Magento\Framework\Data\Form\Element\Factory;

/**
 * Class SiteCodes
 * @package Mageplaza\Worldpay\Block\Adminhtml\Backend
 */
class SiteCodes extends AbstractFieldArray
{
    /**
     * @var Factory
     */
    private $elementFactory;

    /**
     * @var Currency
     */
    private $currencies;

    /**
     * SiteCodes constructor.
     *
     * @param Context $context
     * @param Factory $elementFactory
     * @param Currency $currencies
     * @param array $data
     */
    public function __construct(
        Context $context,
        Factory $elementFactory,
        Currency $currencies,
        array $data = []
    ) {
        $this->elementFactory = $elementFactory;
        $this->currencies     = $currencies;

        parent::__construct($context, $data);
    }

    /**
     * Initialise form fields
     *
     * @return void
     */
    public function _construct()
    {
        $this->addColumn('site_code', ['label' => __('Site Code')]);
        $this->addColumn('currency', ['label' => __('Currency')]);
        $this->addColumn('settlement', ['label' => __('Settlement Currency')]);

        $this->_addAfter       = false;
        $this->_addButtonLabel = __('Add New');

        parent::_construct();
    }

    /**
     * Render array cell for prototypeJS template
     *
     * @param string $columnName
     *
     * @return mixed|string
     * @throws Exception
     */
    public function renderCellTemplate($columnName)
    {
        if (!empty($this->_columns[$columnName])) {
            switch ($columnName) {
                case 'currency':
                case 'settlement':
                    $options = $this->currencies->toOptionArray();
                    break;
                default:
                    $options = '';
                    break;
            }

            if ($options) {
                $element = $this->elementFactory->create('select');
                $element->setForm($this->getForm())
                    ->setName($this->_getCellInputElementName($columnName))
                    ->setHtmlId($this->_getCellInputElementId('<%- _id %>', $columnName))
                    ->setValues($options);

                return str_replace("\n", '', $element->getElementHtml());
            }
        }

        return parent::renderCellTemplate($columnName);
    }
}
