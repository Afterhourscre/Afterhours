<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Controller\Adminhtml\Label;

use Aheadworks\OnSale\Api\Data\LabelInterface;
use Aheadworks\OnSale\Api\LabelRepositoryInterface;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Backend\App\Action as BackendAction;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class InlineEdit
 *
 * @package Aheadworks\OnSale\Controller\Adminhtml\Label
 */
class InlineEdit extends BackendAction
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
     * @var JsonFactory
     */
    private $jsonFactory;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @param Context $context
     * @param LabelRepositoryInterface $labelRepository
     * @param JsonFactory $jsonFactory
     * @param DataObjectHelper $dataObjectHelper
     */
    public function __construct(
        Context $context,
        LabelRepositoryInterface $labelRepository,
        JsonFactory $jsonFactory,
        DataObjectHelper $dataObjectHelper
    ) {
        parent::__construct($context);
        $this->labelRepository = $labelRepository;
        $this->jsonFactory = $jsonFactory;
        $this->dataObjectHelper = $dataObjectHelper;
    }

    /**
     *  {@inheritDoc}
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->jsonFactory->create();
        $error = false;
        $messages = [];

        $postItems = $this->getRequest()->getParam('items', []);
        if (!($this->getRequest()->getParam('isAjax') && count($postItems))) {
            return $resultJson->setData([
                'messages' => [__('Please correct the data sent.')],
                'error' => true,
            ]);
        }

        foreach (array_keys($postItems) as $labelId) {
            try {
                /** @var LabelInterface $label **/
                $label = $this->labelRepository->get($labelId);
                $postData = $postItems[$labelId];
                if (!$error) {
                    $this->setLabelData($label, $postData);
                    $this->labelRepository->save($label);
                }
            } catch (LocalizedException $e) {
                $messages[] = $this->getErrorWithLabelId($label, $e->getMessage());
                $error = true;
            } catch (\RuntimeException $e) {
                $messages[] = $this->getErrorWithLabelId($label, $e->getMessage());
                $error = true;
            } catch (\Exception $e) {
                $messages[] = $this->getErrorWithLabelId(
                    $label,
                    __('Something went wrong while saving the label.')
                );
                $error = true;
            }
        }
        return $resultJson->setData([
            'messages' => $messages,
            'error' => $error
        ]);
    }

    /**
     * Retrieve error message with label id
     *
     * @param LabelInterface $label
     * @param string $errorText
     * @return string
     */
    private function getErrorWithLabelId(LabelInterface $label, $errorText)
    {
        return '[Label ID: ' . $label->getLabelId() . '] ' . $errorText;
    }

    /**
     * Set label data
     *
     * @param LabelInterface $label
     * @param array $labelData
     * @return $this
     */
    private function setLabelData(LabelInterface $label, array $labelData)
    {
        $this->dataObjectHelper->populateWithArray(
            $label,
            $labelData,
            LabelInterface::class
        );
        return $this;
    }
}
