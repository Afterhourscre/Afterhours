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
 * Class Save
 * @package Mageplaza\CallForPrice\Controller\Adminhtml\Rules
 */
class Save extends Rules
{
    /**
     * @return \Magento\Backend\Model\View\Result\Redirect|\Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $redirectBack = $this->getRequest()->getParam('back', false);
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT);
        $data           = $this->getRequest()->getPost('rule');

        if (!$data) {
            return $resultRedirect->setPath('mpcallforprice/*/');
        }

        /** @var \Mageplaza\CallForPrice\Model\Rules $rule */
        $rule = $this->_initRule();

        if (!$this->isRuleExist($rule)) {
            $this->messageManager->addErrorMessage(__('This rule does not exist.'));

            return $resultRedirect->setPath('mpcallforprice/*/');
        }

        if (!empty($data)) {
            $this->prepareData($rule, $data);
            $this->_getSession()->setData('callforprice_rule_data', $data);
        }

        try {
            /** get rule conditions */
            $rule->loadPost($data);
            $rule->save();
            $this->_getSession()->setData('callforprice_rule_data', false);

            $this->messageManager->addSuccessMessage(__('You saved the rule.'));
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $redirectBack = true;
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $redirectBack = true;
            $this->messageManager->addErrorMessage(__('We cannot save the rule.' . $e->getMessage()));
            $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
        }

        return ($redirectBack)
            ? $resultRedirect->setPath('mpcallforprice/*/edit', ['rule_id' => $rule->getRuleId()])
            : $resultRedirect->setPath('mpcallforprice/*/');
    }

    /**
     * @param \Mageplaza\CallForPrice\Model\Rules $model
     *
     * @return bool
     */
    protected function isRuleExist(\Mageplaza\CallForPrice\Model\Rules $model)
    {
        $ruleId = $this->getRequest()->getParam('rule_id');

        return (!$model->getRuleId() && $ruleId) ? false : true;
    }

    /**
     * @param       $rule
     * @param array $data
     *
     * @return $this
     */
    protected function prepareData($rule, $data = [])
    {
        $rule->addData($data);

        return $this;
    }
}