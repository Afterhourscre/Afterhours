<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Acr\Controller\Adminhtml\Rule;

use Aheadworks\Acr\Api\RuleRepositoryInterface;
use Aheadworks\Acr\Model\ResourceModel\Rule\CollectionFactory as RuleCollectionFactory;
use Aheadworks\Acr\Model\Source\Rule\Status as RuleStatus;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;

/**
 * Class MassEnable
 * @package Aheadworks\Acr\Controller\Adminhtml\Rule
 */
class MassEnable extends \Magento\Backend\App\Action
{
    /**
     * {@inheritdoc}
     */
    const ADMIN_RESOURCE = 'Aheadworks_Acr::rules';

    /**
     * @var Filter
     */
    private $filter;

    /**
     * @var RuleCollectionFactory
     */
    private $ruleCollectionFactory;

    /**
     * @var RuleRepositoryInterface
     */
    private $ruleRepository;

    /**
     * @param Context $context
     * @param Filter $filter
     * @param RuleCollectionFactory $ruleCollectionFactory
     * @param RuleRepositoryInterface $ruleRepository
     */
    public function __construct(
        Context $context,
        Filter $filter,
        RuleCollectionFactory $ruleCollectionFactory,
        RuleRepositoryInterface $ruleRepository
    ) {
        parent::__construct($context);
        $this->filter = $filter;
        $this->ruleCollectionFactory = $ruleCollectionFactory;
        $this->ruleRepository = $ruleRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        try {
            $collection = $this->filter->getCollection($this->ruleCollectionFactory->create());
            foreach ($collection->getAllIds() as $ruleId) {
                $rule = $this->ruleRepository->get($ruleId);
                $rule->setStatus(RuleStatus::ENABLED);
                $this->ruleRepository->save($rule);
            }
            $this->messageManager->addSuccessMessage(
                __('A total of %1 record(s) have been enabled.', $collection->getSize())
            );
        } catch (LocalizedException $exception) {
            $this->messageManager->addErrorMessage($exception->getMessage());
        } catch (\Exception $exception) {
            $this->messageManager->addExceptionMessage(
                $exception,
                __('Something went wrong while enabling the items.')
            );
        }

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/');
    }
}
