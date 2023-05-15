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

namespace Mageplaza\CallForPrice\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Psr\Log\LoggerInterface;

/**
 * Class Rules
 * @package Mageplaza\CallForPrice\Controller\Adminhtml
 */
abstract class Rules extends Action
{
    const ADMIN_RESOURCE = 'Mageplaza_CallForPrice::rules';

    /**
     * @type PageFactory
     */
    protected $_resultPageFactory;

    /**
     * @type Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $_logger;

    /**
     * Rules constructor.
     *
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param Registry $coreRegistry
     * @param LoggerInterface $logger
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        Registry $coreRegistry,
        LoggerInterface $logger
    )
    {
        $this->_coreRegistry      = $coreRegistry;
        $this->_resultPageFactory = $resultPageFactory;
        $this->_logger            = $logger;

        parent::__construct($context);
    }

    /**
     * @return \Mageplaza\CallForPrice\Model\Rules
     */
    protected function _initRule()
    {
        $ruleId = (int)$this->getRequest()->getParam('rule_id');
        /** @var \Mageplaza\CallForPrice\Model\Rules $rule */
        $rule = $this->_objectManager->create('Mageplaza\CallForPrice\Model\Rules');
        if ($ruleId) {
            $rule->load($ruleId);
        }
        if (!$this->_coreRegistry->registry('current_rule')) {
            $this->_coreRegistry->register('current_rule', $rule);
        }

        return $rule;
    }
}
