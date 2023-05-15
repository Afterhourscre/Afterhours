<?php
/**
 * Copyright © Mageside. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */

namespace Mageside\MultipleCustomForms\Block\Widget\CustomForm\Fields\Type;

use Mageside\MultipleCustomForms\Model\CustomForm\Field\Settings;

class HiddenType extends \Mageside\MultipleCustomForms\Block\Widget\CustomForm\Fields\Type\DefaultType
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $_registry;
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * HiddenType constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param Settings $fieldSettings
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Customer\Model\Session $customerSession
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Mageside\MultipleCustomForms\Model\CustomForm\Field\Settings $fieldSettings,
        \Magento\Framework\Registry $registry,
        \Magento\Customer\Model\Session $customerSession,
        array $data = []
    ) {
        $this->_registry = $registry;
        $this->_customerSession = $customerSession;
        parent::__construct($context, $fieldSettings, $data);
    }

    /**
     * @return string
     */
    public function getHiddenValue()
    {
        switch ($this->_field->getData(Settings::OPTION_HIDDEN_SOURCE)) {
            case (Settings::OPTION_HIDDEN_PRODUCT_ATTRIBUTE):
                if ($product = $this->_registry->registry('current_product')) {
                    $attribute = $this->_field->getData(Settings::OPTION_HIDDEN_PRODUCT_ATTRIBUTE);
                    return $product->getData($attribute);
                }
                break;
            case (Settings::OPTION_HIDDEN_CATEGORY_ATTRIBUTE):
                if ($category = $this->_registry->registry('current_category')) {
                    $attribute = $this->_field->getData(Settings::OPTION_HIDDEN_CATEGORY_ATTRIBUTE);
                    return $category->getData($attribute);
                }
                break;
            case (Settings::OPTION_HIDDEN_CUSTOMER_ATTRIBUTE):
                if ($this->_customerSession->isLoggedIn()) {
                    $attribute = $this->_field->getData(Settings::OPTION_HIDDEN_CUSTOMER_ATTRIBUTE);
                    return $this->_customerSession->getCustomer()->getData($attribute);
                }
                break;
            case (Settings::OPTION_HIDDEN_STATIC):
                return $this->_field->getData(Settings::OPTION_HIDDEN_STATIC);
                break;
        }

        return '';
    }
}
