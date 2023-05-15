<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Controller\Adminhtml\Rule;

use Aheadworks\OnSale\Api\Data\RuleInterface;
use Aheadworks\OnSale\Api\Data\RuleInterfaceFactory;
use Aheadworks\OnSale\Api\RuleRepositoryInterface;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Request\DataPersistorInterface;
use Aheadworks\OnSale\Ui\DataProvider\Rule\FormDataProvider as RuleFormDataProvider;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Backend\App\Action as BackendAction;

/**
 * Class Save
 *
 * @package Aheadworks\OnSale\Controller\Adminhtml\Rule
 */
class Save extends BackendAction
{
    /**
     * {@inheritdoc}
     */
    const ADMIN_RESOURCE = 'Aheadworks_OnSale::rules';

    /**
     * @var RuleRepositoryInterface
     */
    private $ruleRepository;

    /**
     * @var RuleInterfaceFactory
     */
    private $ruleDataFactory;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @var PostDataProcessor
     */
    private $postDataProcessor;

    /**
     * @var PostDataUpdateChecker
     */
    private $postDataUpdateChecker;

    /**
     * @param Context $context
     * @param RuleRepositoryInterface $ruleRepository
     * @param RuleInterfaceFactory $ruleDataFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataPersistorInterface $dataPersistor
     * @param PostDataProcessor $postDataProcessor
     * @param PostDataUpdateChecker $postDataUpdateChecker
     */
    public function __construct(
        Context $context,
        RuleRepositoryInterface $ruleRepository,
        RuleInterfaceFactory $ruleDataFactory,
        DataObjectHelper $dataObjectHelper,
        DataPersistorInterface $dataPersistor,
        PostDataProcessor $postDataProcessor,
        PostDataUpdateChecker $postDataUpdateChecker
    ) {
        parent::__construct($context);
        $this->ruleRepository = $ruleRepository;
        $this->ruleDataFactory = $ruleDataFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataPersistor = $dataPersistor;
        $this->postDataProcessor = $postDataProcessor;
        $this->postDataUpdateChecker = $postDataUpdateChecker;
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        if ($data = $this->getRequest()->getPostValue()) {
            try {
                $data = $this->postDataProcessor->prepareEntityData($data);
                $rule = $this->performSave($data);

                $this->dataPersistor->clear(RuleFormDataProvider::DATA_PERSISTOR_FORM_DATA_KEY);
                $this->messageManager->addSuccessMessage(__('Rule was successfully saved'));

                if (isset($data['auto_apply']) && (!empty($data['auto_apply']))) {
                    $this->getRequest()->setParam('rule_id', $rule->getRuleId());
                    $this->_forward('applyRules');
                }

                if ($this->getRequest()->getParam('back') == 'edit') {
                    return $resultRedirect->setPath('*/*/edit', ['id' => $rule->getRuleId()]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the rule'));
            }
            $this->dataPersistor->set(RuleFormDataProvider::DATA_PERSISTOR_FORM_DATA_KEY, $data);
            $ruleId = isset($data['rule_id']) ? $data['rule_id'] : false;
            if ($ruleId) {
                return $resultRedirect->setPath('*/*/edit', ['id' => $ruleId, '_current' => true]);
            }
            return $resultRedirect->setPath('*/*/new', ['_current' => true]);
        }
        return $resultRedirect->setPath('*/*/');
    }

    /**
     * Perform save
     *
     * @param array $data
     * @return RuleInterface
     * @throws LocalizedException|\Exception
     */
    private function performSave($data)
    {
        $ruleId = isset($data['rule_id']) ? $data['rule_id'] : false;
        $ruleDataObject = $ruleId
            ? $this->ruleRepository->get($ruleId)
            : $this->ruleDataFactory->create();
        $this->postDataUpdateChecker->checkForReindexNotice($data, $ruleDataObject);
        $this->dataObjectHelper->populateWithArray(
            $ruleDataObject,
            $data,
            RuleInterface::class
        );

        return $this->ruleRepository->save($ruleDataObject);
    }
}
