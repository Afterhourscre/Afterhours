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

namespace Mageplaza\CallForPrice\Controller\Adminhtml\Rules;

use Mageplaza\CallForPrice\Controller\Adminhtml\Rules;

/**
 * Class Edit
 * @package Mageplaza\CallForPrice\Controller\Adminhtml\Rules
 */
class Edit extends Rules
{
    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
        $ruleId = $this->getRequest()->getParam('rule_id');
        $rule   = $this->_initRule();

        if (!$rule->getRuleId() && $ruleId) {
            $this->messageManager->addError(__('This rule no longer exists.'));
            $this->_redirect('mpcallforprice/*/');

            return;
        }

        $data = $this->_getSession()->getData('callforprice_rule_data', true);
        if (!empty($data)) {
            $rule->addData($data);
        }

        $this->_view->loadLayout();
        $this->_setActiveMenu('Mageplaza_CallForPrice::rules');
        $title = $rule->getRuleId() ? $rule->getName() : __('New Rule');
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__($title));

        $this->_view->renderLayout();
    }
}
