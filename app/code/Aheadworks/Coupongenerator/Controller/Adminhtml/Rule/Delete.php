<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Coupongenerator\Controller\Adminhtml\Rule;

/**
 * Class Delete
 * @package Aheadworks\Coupongenerator\Controller\Adminhtml\Rule
 */
class Delete extends \Magento\Backend\App\Action
{
    /**
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Aheadworks_Coupongenerator::manage_rules';

    /**
     * @var \Magento\SalesRule\Api\RuleRepositoryInterface
     */
    private $magentoSalesRuleRepository;

    /**
     * @var \Aheadworks\Coupongenerator\Model\SalesruleRepository;
     */
    private $salesruleRepository;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\SalesRule\Api\RuleRepositoryInterface $magentoSalesRuleRepository
     * @param \Aheadworks\Coupongenerator\Model\SalesruleRepository $salesruleRepository
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\SalesRule\Api\RuleRepositoryInterface $magentoSalesRuleRepository,
        \Aheadworks\Coupongenerator\Model\SalesruleRepository $salesruleRepository
    ) {
        $this->magentoSalesRuleRepository = $magentoSalesRuleRepository;
        $this->salesruleRepository = $salesruleRepository;
        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $id = $this->getRequest()->getParam('id');
        if ($id) {
            try {
                /** @var \Aheadworks\Coupongenerator\Api\Data\SalesruleInterface $salesruleDataObject */
                $salesruleDataObject = $this->salesruleRepository->get($id);

                $this->magentoSalesRuleRepository->deleteById($salesruleDataObject->getRuleId());

                $this->messageManager->addSuccessMessage(__('Rule was successfully deleted'));
                return $resultRedirect->setPath('*/*/index');
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }
        }
        $this->messageManager->addErrorMessage(__('Rule can\'t be deleted'));
        return $resultRedirect->setPath('*/*/index');
    }
}
