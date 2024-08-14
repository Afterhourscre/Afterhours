<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Acr\Controller\Adminhtml\Rule;

use Aheadworks\Acr\Api\Data\RuleInterface;
use Aheadworks\Acr\Api\Data\RuleInterfaceFactory;
use Aheadworks\Acr\Api\RuleRepositoryInterface;
use Aheadworks\Acr\Model\Rule\Converter as RuleConverter;
use Aheadworks\Acr\Api\QueueManagementInterface;
use Aheadworks\Acr\Model\Exception\TestRecipientNotSpecified;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Save
 * @package Aheadworks\Acr\Controller\Adminhtml\Rule
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Save extends \Magento\Backend\App\Action
{
    /**
     * {@inheritdoc}
     */
    const ADMIN_RESOURCE = 'Aheadworks_Acr::rules';

    /**
     * @var RuleRepositoryInterface
     */
    private $ruleRepository;

    /**
     * @var RuleInterfaceFactory
     */
    private $ruleFactory;

    /**
     * @var RuleConverter
     */
    private $ruleConverter;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @var QueueManagementInterface
     */
    private $queueManagement;

    /**
     * @param Context $context
     * @param RuleRepositoryInterface $ruleRepository
     * @param RuleInterfaceFactory $ruleFactory
     * @param RuleConverter $ruleConverter
     * @param DataObjectHelper $dataObjectHelper
     * @param DataPersistorInterface $dataPersistor
     * @param QueueManagementInterface $queueManagement
     */
    public function __construct(
        Context $context,
        RuleRepositoryInterface $ruleRepository,
        RuleInterfaceFactory $ruleFactory,
        RuleConverter $ruleConverter,
        DataObjectHelper $dataObjectHelper,
        DataPersistorInterface $dataPersistor,
        QueueManagementInterface $queueManagement
    ) {
        parent::__construct($context);
        $this->ruleRepository = $ruleRepository;
        $this->ruleFactory = $ruleFactory;
        $this->ruleConverter = $ruleConverter;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataPersistor = $dataPersistor;
        $this->queueManagement = $queueManagement;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            try {
                $id = isset($data['id']) ? $data['id'] : false;
                $preparedData = $this->prepareData($data);

                /** @var RuleInterface $ruleDataObject */
                $ruleDataObject = $id
                    ? $this->ruleRepository->get($id)
                    : $this->ruleFactory->create();

                $this->dataObjectHelper->populateWithArray(
                    $ruleDataObject,
                    $preparedData,
                    RuleInterface::class
                );

                $ruleDataObject = $this->ruleRepository->save($ruleDataObject);

                $this->messageManager->addSuccessMessage(__('Rule was successfully saved.'));

                if ($this->getRequest()->getParam('sendtest')) {
                    $this->queueManagement->sendTest($ruleDataObject);
                    $this->messageManager->addSuccessMessage(__('Email was successfully sent.'));
                }

                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['id' => $ruleDataObject->getId()]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (TestRecipientNotSpecified $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage(
                    $e,
                    __('Something went wrong while saving the rule data.')
                );
            }
            unset($data['sendtest']);
            $this->dataPersistor->set('aw_acr_rule', $data);
            if ($id) {
                return $resultRedirect->setPath('*/*/edit', ['id' => $id, '_current' => true]);
            }
            return $resultRedirect->setPath('*/*/new', ['_current' => true]);
        }
        return $resultRedirect->setPath('*/*/');
    }

    /**
     * Prepare data before save
     *
     * @param array $data
     * @return array
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    private function prepareData(array $data)
    {
        $preparedData = [];
        foreach ($data as $key => $value) {
            if ($key == RuleInterface::CART_CONDITIONS || $key == RuleInterface::PRODUCT_CONDITIONS) {
                continue;
            } elseif ($key == 'rule') {
                $preparedData[RuleInterface::CART_CONDITIONS] = $this->ruleConverter->getCartConditions(
                    $data['rule']
                );
                $preparedData[RuleInterface::PRODUCT_CONDITIONS] = $this->ruleConverter->getProductConditions(
                    $data['rule']
                );
            } elseif ($key == 'customer_groups'
                || $key == 'product_type_ids'
            ) {
                $allFound = false;
                foreach ($value as $groupValue) {
                    if ($groupValue == 'all') {
                        $allFound = true;
                        $preparedData[$key] = ['all'];
                        break;
                    }
                }
                if (!$allFound) {
                    $preparedData[$key] = $value;
                }
            } elseif ($key == 'store_ids') {
                $allStoresFound = false;
                foreach ($value as $storeValue) {
                    if ($storeValue == 0) {
                        $allStoresFound = true;
                        $preparedData[$key] = [0];
                        break;
                    }
                }
                if (!$allStoresFound) {
                    $preparedData[$key] = $value;
                }
            } else {
                $preparedData[$key] = $value;
            }
        }
        return $preparedData;
    }
}
