<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Controller\Adminhtml\Rule;

use Magento\Backend\App\Action as BackendAction;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Aheadworks\OnSale\Model\ResourceModel\Rule\Collection;
use Aheadworks\OnSale\Model\ResourceModel\Rule\CollectionFactory;
use Aheadworks\OnSale\Api\RuleRepositoryInterface;
use Aheadworks\OnSale\Api\LabelRepositoryInterface;
use Magento\Framework\Controller\ResultFactory;

/**
 * Class AbstractMassAction
 *
 * @package Aheadworks\OnSale\Controller\Adminhtml\Rule
 */
abstract class AbstractMassAction extends BackendAction
{
    /**
     * {@inheritdoc}
     */
    const ADMIN_RESOURCE = 'Aheadworks_OnSale::rules';

    /**
     * @var Filter
     */
    private $filter;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var RuleRepositoryInterface
     */
    protected $ruleRepository;

    /**
     * @var LabelRepositoryInterface
     */
    protected $labelRepository;

    /**
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param RuleRepositoryInterface $ruleRepository
     * @param LabelRepositoryInterface $labelRepository
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        RuleRepositoryInterface $ruleRepository,
        LabelRepositoryInterface $labelRepository
    ) {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->ruleRepository = $ruleRepository;
        $this->labelRepository = $labelRepository;
        parent::__construct($context);
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        try {
            /** @var Collection $collection */
            $collection = $this->filter->getCollection($this->collectionFactory->create());
            return $this->massAction($collection);
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            return $resultRedirect->setPath('*/*/index');
        }
    }

    /**
     * Performs mass action
     *
     * @param Collection $collection
     * @return \Magento\Framework\Controller\ResultInterface|\Magento\Framework\App\ResponseInterface
     */
    abstract protected function massAction(Collection $collection);
}
