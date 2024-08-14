<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Coupongenerator\Controller\Adminhtml\Rule;

use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class Edit
 * @package Aheadworks\Coupongenerator\Controller\Adminhtml\Rule
 */
class Edit extends \Magento\Backend\App\Action
{
    /**
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Aheadworks_Coupongenerator::manage_rules';

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    private $resultPageFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    private $coreRegistry;

    /**
     * @var \Magento\SalesRule\Api\RuleRepositoryInterface
     */
    private $magentoSalesRuleRepository;

    /**
     * @var \Magento\SalesRule\Api\Data\RuleInterfaceFactory $magentoSalesRuleFactory
     */
    private $magentoSalesRuleFactory;

    /**
     * @var \Aheadworks\Coupongenerator\Model\SalesruleRepository
     */
    private $salesruleRepository;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\SalesRule\Api\RuleRepositoryInterface $magentoSalesRuleRepository
     * @param \Magento\SalesRule\Api\Data\RuleInterfaceFactory $magentoSalesRuleFactory
     * @param \Aheadworks\Coupongenerator\Model\SalesruleRepository $salesruleRepository
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\SalesRule\Api\RuleRepositoryInterface $magentoSalesRuleRepository,
        \Magento\SalesRule\Api\Data\RuleInterfaceFactory $magentoSalesRuleFactory,
        \Aheadworks\Coupongenerator\Model\SalesruleRepository $salesruleRepository
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->coreRegistry = $coreRegistry;
        $this->magentoSalesRuleRepository = $magentoSalesRuleRepository;
        $this->magentoSalesRuleFactory = $magentoSalesRuleFactory;
        $this->salesruleRepository = $salesruleRepository;
        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $magentoRuleDataObject = $this->magentoSalesRuleFactory->create();
        $id = $this->getRequest()->getParam('id');
        if ($id) {
            try {
                /** @var \Aheadworks\Coupongenerator\Api\Data\SalesruleInterface $salesruleDataObject */
                $salesruleDataObject = $this->salesruleRepository->get($id);

                /** @var \Magento\SalesRule\Api\Data\RuleInterface $magentoRule */
                $magentoRuleDataObject = $this->magentoSalesRuleRepository->getById($salesruleDataObject->getRuleId());
            } catch (NoSuchEntityException $exception) {
                $this->messageManager->addExceptionMessage(
                    $exception,
                    __('This rule no longer exists')
                );
                $resultRedirect = $this->resultRedirectFactory->create();
                $resultRedirect->setPath('*/*/index');
                return $resultRedirect;
            }
        }
        $this->coreRegistry->register('aw_coupongenerator_rule', $magentoRuleDataObject);

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Aheadworks_Coupongenerator::main');
        $resultPage->getConfig()->getTitle()->prepend(
            $magentoRuleDataObject->getRuleId() ?  __('Edit Rule') : __('New Rule')
        );

        return $resultPage;
    }
}
