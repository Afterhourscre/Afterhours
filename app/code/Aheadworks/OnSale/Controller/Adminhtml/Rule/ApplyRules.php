<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Controller\Adminhtml\Rule;

use Aheadworks\OnSale\Model\Rule\Job as RuleJob;
use Aheadworks\OnSale\Model\Rule\ReindexNotice;
use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;

/**
 * Class ApplyRules
 *
 * @package Aheadworks\OnSale\Controller\Adminhtml\Rule
 */
class ApplyRules extends Action
{
    /**
     * @var RuleJob
     */
    private $ruleJob;

    /**
     * @var ReindexNotice
     */
    private $reindexNotice;

    /**
     * @param Context $context
     * @param RuleJob $ruleJob
     * @param ReindexNotice $reindexNotice
     */
    public function __construct(
        Context $context,
        RuleJob $ruleJob,
        ReindexNotice $reindexNotice
    ) {
        parent::__construct($context);
        $this->ruleJob = $ruleJob;
        $this->reindexNotice = $reindexNotice;
    }

    /**
     * Apply all active catalog price rules
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $errorMessage = __('We can\'t apply the rules.');
        try {
            $this->ruleJob->applyAll();

            if ($this->ruleJob->hasSuccess()) {
                $this->messageManager->addSuccessMessage($this->ruleJob->getSuccess());
                $this->reindexNotice->setDisabled();
            } elseif ($this->ruleJob->hasError()) {
                $this->messageManager->addErrorMessage($errorMessage . ' ' . $this->ruleJob->getError());
            }
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e, __('Something went wrong while applying the rules'));
        }

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/');
    }
}
