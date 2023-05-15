<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Helpdesk\Controller\Adminhtml\QuickResponse;

use Aheadworks\Helpdesk\Api\QuickResponseRepositoryInterface;
use Aheadworks\Helpdesk\Api\Data\QuickResponseInterface;
use Aheadworks\Helpdesk\Api\Data\QuickResponseInterfaceFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Backend\App\Action;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\App\Request\DataPersistorInterface;

/**
 * Class Save
 *
 * @package Aheadworks\Helpdesk\Controller\Adminhtml\QuickResponse
 */
class Save extends Action
{
    /**
     * {@inheritdoc}
     */
    const ADMIN_RESOURCE = 'Aheadworks_Helpdesk::quick_responses';

    /**
     * @var QuickResponseRepositoryInterface
     */
    private $quickResponseRepository;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @var QuickResponseInterfaceFactory
     */
    private $quickResponseFactory;

    /**
     * @var PostDataProcessor
     */
    private $postDataProcessor;

    /**
     * @param Context $context
     * @param QuickResponseRepositoryInterface $quickResponseRepository
     * @param QuickResponseInterfaceFactory $quickResponseFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataPersistorInterface $dataPersistor
     * @param PostDataProcessor $postDataProcessor
     */
    public function __construct(
        Context $context,
        QuickResponseRepositoryInterface $quickResponseRepository,
        QuickResponseInterfaceFactory $quickResponseFactory,
        DataObjectHelper $dataObjectHelper,
        DataPersistorInterface $dataPersistor,
        PostDataProcessor $postDataProcessor
    ) {
        parent::__construct($context);
        $this->quickResponseFactory = $quickResponseFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataPersistor = $dataPersistor;
        $this->quickResponseRepository = $quickResponseRepository;
        $this->postDataProcessor = $postDataProcessor;
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
                $quickResponse = $this->performSave($data);

                $this->dataPersistor->clear('aw_helpdesk_quick_response');
                $this->messageManager->addSuccessMessage(__('Quick response has been successfully saved.'));

                if ($this->getRequest()->getParam('back') == 'edit') {
                    return $resultRedirect->setPath('*/*/edit', ['id' => $quickResponse->getId()]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage(
                    $e,
                    __('Something went wrong while saving the quick response.')
                );
            }
            $this->dataPersistor->set('aw_helpdesk_quick_response', $data);
            $id = isset($data['id']) ? $data['id'] : false;
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
     * @return QuickResponseInterface
     */
    private function performSave($data)
    {
        $id = isset($data['id']) ? $data['id'] : false;
        $dataObject = $id
            ? $this->quickResponseRepository->get($id)
            : $this->quickResponseFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $dataObject,
            $data,
            QuickResponseInterface::class
        );

        return $this->quickResponseRepository->save($dataObject);
    }
}
