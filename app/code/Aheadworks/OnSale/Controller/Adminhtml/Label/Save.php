<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Controller\Adminhtml\Label;

use Aheadworks\OnSale\Api\LabelRepositoryInterface;
use Aheadworks\OnSale\Api\Data\LabelInterface;
use Aheadworks\OnSale\Api\Data\LabelInterfaceFactory;
use Aheadworks\OnSale\Ui\DataProvider\Label\FormDataProvider as LabelFormDataProvider;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Backend\App\Action;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\App\Request\DataPersistorInterface;
use Aheadworks\OnSale\Model\Label\Copier as LabelCopier;

/**
 * Class Save
 *
 * @package Aheadworks\OnSale\Controller\Adminhtml\Label
 */
class Save extends Action
{
    /**
     * {@inheritdoc}
     */
    const ADMIN_RESOURCE = 'Aheadworks_OnSale::labels';

    /**
     * @var LabelRepositoryInterface
     */
    private $labelRepository;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @var LabelInterfaceFactory
     */
    private $labelInterfaceFactory;

    /**
     * @var PostDataProcessor
     */
    private $postDataProcessor;

    /**
     * @var LabelCopier
     */
    private $labelCopier;

    /**
     * @param Context $context
     * @param LabelRepositoryInterface $labelRepository
     * @param LabelInterfaceFactory $labelInterfaceFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataPersistorInterface $dataPersistor
     * @param PostDataProcessor $postDataProcessor
     * @param LabelCopier $labelCopier
     */
    public function __construct(
        Context $context,
        LabelRepositoryInterface $labelRepository,
        LabelInterfaceFactory $labelInterfaceFactory,
        DataObjectHelper $dataObjectHelper,
        DataPersistorInterface $dataPersistor,
        PostDataProcessor $postDataProcessor,
        LabelCopier $labelCopier
    ) {
        parent::__construct($context);
        $this->labelRepository = $labelRepository;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataPersistor = $dataPersistor;
        $this->labelInterfaceFactory = $labelInterfaceFactory;
        $this->postDataProcessor = $postDataProcessor;
        $this->labelCopier = $labelCopier;
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data = $this->getRequest()->getPostValue()) {
            try {
                $data = $this->postDataProcessor->prepareEntityData($data);

                $label = $this->performSave($data);

                $this->dataPersistor->clear(LabelFormDataProvider::DATA_PERSISTOR_FORM_DATA_KEY);
                $this->messageManager->addSuccessMessage(__('You saved the label.'));

                $backParam = $this->getRequest()->getParam('back');
                if ($backParam == 'edit') {
                    return $resultRedirect->setPath('*/*/edit', ['id' => $label->getId()]);
                }
                if ($backParam == 'duplicate') {
                    $newLabel = $this->labelCopier->copy($label);
                    $this->messageManager->addSuccessMessage(__('You duplicated the label.'));
                    return $resultRedirect->setPath('*/*/edit', ['id' => $newLabel->getId()]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage(
                    $e,
                    __('Something went wrong while saving the label.')
                );
            }
            $this->dataPersistor->set(LabelFormDataProvider::DATA_PERSISTOR_FORM_DATA_KEY, $data);
            $id = isset($data['label_id']) ? $data['label_id'] : false;
            if ($id) {
                return $resultRedirect->setPath('*/*/edit', ['id' => $id, '_current' => true]);
            }
            return $resultRedirect->setPath('*/*/new', ['_current' => true]);
        }
        return $resultRedirect->setPath('*/*/');
    }

    /**
     * Perform save
     *
     * @param array $data
     * @return LabelInterface
     * @throws LocalizedException | \Exception
     */
    private function performSave($data)
    {
        $id = isset($data['id']) ? $data['id'] : false;
        $labelObject = $id
            ? $this->labelRepository->get($id)
            : $this->labelInterfaceFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $labelObject,
            $data,
            LabelInterface::class
        );

        return $this->labelRepository->save($labelObject);
    }
}
