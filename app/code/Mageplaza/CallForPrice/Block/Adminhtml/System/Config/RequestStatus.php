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

namespace Mageplaza\CallForPrice\Block\Adminhtml\System\Config;

use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use Magento\Framework\Data\Form\Element\Factory;

/**
 * Class RequestStatus
 * @package Mageplaza\CallForPrice\Block\Adminhtml\System\Config
 */
class RequestStatus extends AbstractFieldArray
{
    /**
     * @var string
     */
    protected $_template = 'system/config/form/field/request_status.phtml';

    /**
     * @var Factory
     */
    protected $elementFactory;

    /**
     * Rows cache
     *
     * @var array|null
     */
    private $_arrayRowsCache;

    /**
     * RequestStatus constructor.
     *
     * @param Context $context
     * @param Factory $elementFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        Factory $elementFactory,
        array $data = []
    )
    {
        $this->elementFactory = $elementFactory;

        parent::__construct($context, $data);
    }

    /**
     * Initialise form fields
     *
     * @return void
     */
    public function _construct()
    {
        $this->addColumn('labelstatus', ['label' => __('Label')]);
        $this->addColumn('isdefault', ['label' => __('Is Default')]);
        $this->_addAfter = false;

        parent::_construct();
    }

    /**
     * @param string $columnName
     *
     * @return mixed|string
     * @throws \Exception
     */
    public function renderCellTemplate($columnName)
    {
        if (!empty($this->_columns[$columnName]) && $columnName == 'isdefault') {
            $element = $this->elementFactory->create('radio');
            $element->setForm($this->getForm())
                ->setName($this->_getCellInputElementName($columnName))
                ->setHtmlId($this->_getCellInputElementId('<%- _id %>', $columnName))
                ->setValue(1);

            return str_replace("\n", '', $element->getElementHtml());
        }

        return parent::renderCellTemplate($columnName);
    }

    /**
     * Get Button Label
     *
     * @return \Magento\Framework\Phrase
     */
    public function getAddButtonLabel()
    {
        return __('Add');
    }

    /**
     * Obtain existing data from form element
     *
     * Each row will be instance of \Magento\Framework\DataObject
     *
     * @return array
     */
    public function getArrayRows()
    {
        if (null !== $this->_arrayRowsCache) {
            return $this->_arrayRowsCache;
        }
        $result = [];

        $elementValue = $this->getElement()->getValue();
        if ($elementValue && is_array($elementValue)) {
            foreach ($elementValue as $rowId => $row) {
                $rowColumnValues = [];
                foreach ($row as $key => $value) {
                    $row[$key]                                                    = $value;
                    $rowColumnValues[$this->_getCellInputElementId($rowId, $key)] = $row[$key];
                }
                $row['_id']           = $rowId;
                $row['column_values'] = $rowColumnValues;
                $result[$rowId]       = new \Magento\Framework\DataObject($row);
                $this->_prepareArrayRow($result[$rowId]);
            }
        }
        $this->_arrayRowsCache = $result;

        return $this->_arrayRowsCache;
    }
}
