<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Coupongenerator\Block\Adminhtml\Customer;

use Magento\Customer\Controller\RegistryConstants;
use Magento\Ui\Component\Layout\Tabs\TabInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;

/**
 * Customer account form block
 *
 * Class Generate
 * @package Aheadworks\Coupongenerator\Block\Adminhtml\Customer
 * @codeCoverageIgnore
 */
class Generate extends \Magento\Backend\Block\Widget\Form\Generic implements TabInterface
{
    /**
     * @var \Aheadworks\Coupongenerator\Model\ResourceModel\Salesrule\CollectionFactory
     */
    private $salesruleCollectionFactory;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Aheadworks\Coupongenerator\Model\ResourceModel\Salesrule\CollectionFactory $salesruleCollectionFactory
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Aheadworks\Coupongenerator\Model\ResourceModel\Salesrule\CollectionFactory $salesruleCollectionFactory,
        CustomerRepositoryInterface $customerRepository,
        array $data = []
    ) {
        $this->salesruleCollectionFactory = $salesruleCollectionFactory;
        $this->customerRepository = $customerRepository;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getTabLabel()
    {
        return __('Coupons');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return __('Coupons');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabClass()
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function getTabUrl()
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function isAjaxLoaded()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return $this->_coreRegistry->registry(RegistryConstants::CURRENT_CUSTOMER_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Initialize the form
     *
     * @return $this
     */
    public function initForm()
    {
        if (!$this->canShowTab()) {
            return $this;
        }
        /**@var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('coupongenerate_');
        $fieldset = $form->addFieldset('base_customer_fieldset', ['legend' => __('Generate Coupon')]);
        $fieldset->addField('customer_note', 'note', ['text' => __('Click "Save Customer" to generate a coupon')]);

        $customerId = $this->_coreRegistry->registry(RegistryConstants::CURRENT_CUSTOMER_ID);
        /** @var \Magento\Customer\Api\Data\CustomerInterface $customer */
        $customer = $this->customerRepository->getById($customerId);

        $activeRules = $this->salesruleCollectionFactory->create()
            ->setActiveRules()
            ->addWebsiteFilter($customer->getWebsiteId())
            ->toOptionArray()
        ;
        array_unshift(
            $activeRules,
            ['value' => 0, 'label' => __('Please select')]
        );

        $fieldset->addField(
            'rule_id',
            'select',
            [
                'label' => __('Rule'),
                'title' => __('Rule'),
                'name' => 'rule_id',
                'data-form-part' => $this->getData('target_form'),
                'values' => $activeRules
            ]
        );

        $fieldset->addField(
            'send_email_with_coupon',
            'checkbox',
            [
                'label' => __('Send Email with Coupon'),
                'title' => __('Send Email with Coupon'),
                'name' => 'send_email_with_coupon',
                'checked' => true,
                'data-form-part' => $this->getData('target_form'),
                'value' => true,
                'onchange' => 'this.value = this.checked;'
            ]
        );

        $this->setForm($form);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function _toHtml()
    {
        if ($this->canShowTab()) {
            $this->initForm();
            return parent::_toHtml();
        } else {
            return '';
        }
    }
}
