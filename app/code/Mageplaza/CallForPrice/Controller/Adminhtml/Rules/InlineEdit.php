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

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Exception\LocalizedException;
use Mageplaza\CallForPrice\Model\RulesFactory;

/**
 * Class InlineEdit
 * @package Mageplaza\CallForPrice\Controller\Adminhtml\Rules
 */
class InlineEdit extends Action
{
    /**
     * @var JsonFactory
     */
    public $jsonFactory;

    /**
     * @var RulesFactory
     */
    public $ruleFactory;

    /**
     * InlineEdit constructor.
     *
     * @param Context $context
     * @param JsonFactory $jsonFactory
     * @param RulesFactory $ruleFactory
     */
    public function __construct(
        Context $context,
        JsonFactory $jsonFactory,
        RulesFactory $ruleFactory
    )
    {
        $this->jsonFactory = $jsonFactory;
        $this->ruleFactory = $ruleFactory;

        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->jsonFactory->create();
        $error      = false;
        $messages   = [];
        $ruleItems  = $this->getRequest()->getParam('items', []);
        if (!($this->getRequest()->getParam('isAjax') && !empty($ruleItems))) {
            return $resultJson->setData([
                'messages' => [__('Please correct the data sent.')],
                'error'    => true,
            ]);
        }

        $key    = array_keys($ruleItems);
        $ruleId = !empty($key) ? (int)$key[0] : '';
        /** @var \Mageplaza\CallForPrice\Model\Rules $rule */
        $rule = $this->ruleFactory->create()->load($ruleId);
        try {
            $ruleData = $ruleItems[$ruleId];
            $rule->addData($ruleData)->save();
        } catch (LocalizedException $e) {
            $messages[] = $this->getErrorWithRuleId($rule, $e->getMessage());
            $error      = true;
        } catch (\RuntimeException $e) {
            $messages[] = $this->getErrorWithRuleId($rule, $e->getMessage());
            $error      = true;
        } catch (\Exception $e) {
            $messages[] = $this->getErrorWithRuleId($rule, __('Something went wrong while saving the Rule.'));
            $error      = true;
        }

        return $resultJson->setData([
            'messages' => $messages,
            'error'    => $error
        ]);
    }

    /**
     * Add Rule id to error message
     *
     * @param \Mageplaza\CallForPrice\Model\Rules $rule
     * @param string $errorText
     *
     * @return string
     */
    public function getErrorWithRuleId(\Mageplaza\CallForPrice\Model\Rules $rule, $errorText)
    {
        return '[Rule ID: ' . $rule->getRuleId() . '] ' . $errorText;
    }
}
