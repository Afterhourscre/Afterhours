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

namespace Mageplaza\CallForPrice\Block\Adminhtml\Requests\Edit\Tab;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Config\Model\Config\Source\Yesno;
use Magento\Customer\Api\GroupRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Convert\DataObject;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime;
use Magento\Store\Model\System\Store;
use Mageplaza\CallForPrice\Model\RequestState;

/**
 * Class General
 * @package Mageplaza\CallForPrice\Block\Adminhtml\Requests\Edit\Tab
 */
class General extends Generic implements TabInterface
{
    /**
     * @var Yesno
     */
    protected $_yesno;

    /**
     * @var RequestState
     */
    protected $_status;

    /**
     * @var GroupRepositoryInterface
     */
    protected $_groupRepository;

    /**
     * @var Store
     */
    public $_systemStore;

    /**
     * @var DataObject
     */
    protected $_objectConverter;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $_searchCriteriaBuilder;

    /**
     * General constructor.
     *
     * @param Yesno $yesno
     * @param RequestState $status
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param GroupRepositoryInterface $groupRepository
     * @param Store $systemStore
     * @param DataObject $objectConverter
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param array $data
     */
    public function __construct(
        Yesno $yesno,
        RequestState $status,
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        GroupRepositoryInterface $groupRepository,
        Store $systemStore,
        DataObject $objectConverter,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        array $data = []
    )
    {
        $this->_yesno                 = $yesno;
        $this->_status                = $status;
        $this->_groupRepository       = $groupRepository;
        $this->_systemStore           = $systemStore;
        $this->_objectConverter       = $objectConverter;
        $this->_searchCriteriaBuilder = $searchCriteriaBuilder;

        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * @return Generic
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Zend_Serializer_Exception
     */
    protected function _prepareForm()
    {
        /** @var \Mageplaza\CallForPrice\Model\Requests $request */
        $request = $this->_coreRegistry->registry('current_request');
        $form    = $this->_formFactory->create();
        $form->setHtmlIdPrefix('request_');
        $form->setFieldNameSuffix('request');

        $fieldset = $form->addFieldset('base_fieldset', [
            'legend' => __('Request Information'),
            'class'  => 'fieldset-wide'
        ]);

        $requestId = $this->getRequest()->getParam('request_id');

        if ($requestId) {
            $dateFormat = $this->_localeDate->getDateFormat(\IntlDateFormatter::SHORT);
            $fieldset->addField('created_at', 'label', [
                'name'         => 'created_at',
                'label'        => __('Requested at'),
                'title'        => __('Requested at'),
                'input_format' => DateTime::DATE_INTERNAL_FORMAT,
                'date_format'  => $dateFormat
            ]);

            $fieldset->addField('product_requested', 'Mageplaza\CallForPrice\Block\Adminhtml\Requests\Renderer\Product', [
                'name'    => 'product_requested',
                'label'   => __('Product Requested'),
                'title'   => __('Product Requested'),
                'subject' => $this,
            ]);
        }

        $fieldset->addField('sku', 'label', [
            'name'  => 'sku',
            'label' => __('SKU'),
            'title' => __('SKU'),
        ]);

        /** @var \Magento\Framework\Data\Form\Element\Renderer\RendererInterface $rendererBlock */
        if (!$this->_storeManager->isSingleStoreMode()) {
            /** @var \Magento\Framework\Data\Form\Element\Renderer\RendererInterface $rendererBlock */
            $rendererBlock = $this->getLayout()->createBlock('Magento\Backend\Block\Store\Switcher\Form\Renderer\Fieldset\Element');
            $fieldset->addField('store_ids', 'multiselect', [
                'name'     => 'store_ids',
                'label'    => __('Store Views'),
                'title'    => __('Store Views'),
                'required' => true,
                'values'   => $this->_systemStore->getStoreValuesForForm(false, true)
            ])->setRenderer($rendererBlock);

            if (!$request->hasData('store_ids')) {
                $request->setStoreIds(0);
            }
        } else {
            $fieldset->addField('store_ids', 'hidden', [
                'name'  => 'store_ids',
                'value' => $this->_storeManager->getStore()->getId()
            ]);
        }

        $fieldset->addField('status', 'select', [
            'name'     => 'status',
            'label'    => __('Status'),
            'title'    => __('Status'),
            'required' => true,
            'values'   => $this->_status->toOptionArray()
        ]);

        $fieldset->addField('name', 'text', [
            'name'  => 'name',
            'label' => __('Name'),
            'title' => __('Name'),
        ]);

        $fieldset->addField('email', 'text', [
            'name'  => 'email',
            'label' => __('Email'),
            'title' => __('Email'),
        ]);

        $fieldset->addField('phone', 'text', [
            'name'  => 'phone',
            'label' => __('Phone'),
            'title' => __('Phone'),
        ]);

        $fieldset->addField('customer_note', 'textarea', [
            'name'  => 'customer_note',
            'label' => __('Customer Note'),
            'title' => __('Customer Note'),
        ]);

        $fieldset->addField('internal_note', 'textarea', [
            'name'  => 'internal_note',
            'label' => __('Internal Note'),
            'title' => __('Internal Note'),
            'note'  => __('This note can be seen by internal team only'),
        ]);

        $form->addValues($request->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('General');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->getTabLabel();
    }

    /**
     * Can show tab in tabs
     *
     * @return boolean
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Tab is hidden
     *
     * @return boolean
     */
    public function isHidden()
    {
        return false;
    }
}
