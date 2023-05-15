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
 * Class Delete
 * @package Mageplaza\CallForPrice\Controller\Adminhtml\Rules
 */
class Delete extends Rules
{
    /**
     * execute action
     *
     * @return void
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('rule_id');
        if ($id) {
            try {
                /** @var \Mageplaza\CallForPrice\Model\Rules $rule */
                $rule = $this->_objectManager->create('Mageplaza\CallForPrice\Model\Rules');
                $rule->load($id);
                $rule->delete();

                $this->messageManager->addSuccessMessage(__('The rule has been deleted.'));
                $this->_redirect('mpcallforprice/*/');

                return;
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(
                    __('Something went wrong while deleting rule data. Please review the action log and try again.')
                );
                $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);

                $this->_redirect('mpcallforprice/*/edit', ['id' => $id]);

                return;
            }
        }

        $this->messageManager->addErrorMessage(__('We cannot find a rule to delete.'));
        $this->_redirect('mpcallforprice/*/');
    }
}
