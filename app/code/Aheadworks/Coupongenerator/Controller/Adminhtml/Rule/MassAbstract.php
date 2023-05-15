<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Coupongenerator\Controller\Adminhtml\Rule;

use Magento\Backend\App\Action\Context;
use Magento\SalesRule\Api\RuleRepositoryInterface;
use Aheadworks\Coupongenerator\Model\ResourceModel\Salesrule\CollectionFactory;

/**
 * Class MassAbstract
 * @package \Aheadworks\Coupongenerator\Controller\Adminhtml\Rule
 */
abstract class MassAbstract extends \Magento\Backend\App\Action
{
    /**
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Aheadworks_Coupongenerator::manage_rules';

    /**
     * @var \Magento\SalesRule\Api\RuleRepositoryInterface
     */
    protected $magentoSalesRuleRepository;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var \Magento\Ui\Component\MassAction\Filter
     */
    protected $filter;

    /**
     * @var string
     */
    protected $errorMessage = 'Something went wrong while perform mass action';

    /**
     * @param Context $context
     * @param CollectionFactory $collectionFactory
     * @param \Magento\SalesRule\Api\RuleRepositoryInterface $magentoSalesRuleRepository
     * @param \Magento\Ui\Component\MassAction\Filter $filter
     */
    public function __construct(
        Context $context,
        CollectionFactory $collectionFactory,
        RuleRepositoryInterface $magentoSalesRuleRepository,
        \Magento\Ui\Component\MassAction\Filter $filter
    ) {
        parent::__construct($context);
        $this->collectionFactory = $collectionFactory;
        $this->magentoSalesRuleRepository = $magentoSalesRuleRepository;
        $this->filter = $filter;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        try {
            $collection = $this->filter->getCollection($this->collectionFactory->create());
            $this->massAction($collection);
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('*/*/index');
        return $resultRedirect;
    }

    /**
     * Performs mass action
     *
     * @param \Aheadworks\Coupongenerator\Model\ResourceModel\Salesrule\Collection $collection
     * @return void
     */
    abstract protected function massAction($collection);
}
