<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Controller\Adminhtml\Rule;

use Aheadworks\OnSale\Api\Data\RuleInterface;
use Aheadworks\OnSale\Api\RuleRepositoryInterface;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Backend\App\Action as BackendAction;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class InlineEdit
 *
 * @package Aheadworks\OnSale\Controller\Adminhtml\Rule
 */
class InlineEdit extends BackendAction
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
     * @var JsonFactory
     */
    private $jsonFactory;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var PostDataUpdateChecker
     */
    private $postDataUpdateChecker;

    /**
     * @param Context $context
     * @param RuleRepositoryInterface $ruleRepository
     * @param JsonFactory $jsonFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param PostDataUpdateChecker $postDataUpdateChecker
     */
    public function __construct(
        Context $context,
        RuleRepositoryInterface $ruleRepository,
        JsonFactory $jsonFactory,
        DataObjectHelper $dataObjectHelper,
        PostDataUpdateChecker $postDataUpdateChecker
    ) {
        parent::__construct($context);
        $this->ruleRepository = $ruleRepository;
        $this->jsonFactory = $jsonFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->postDataUpdateChecker = $postDataUpdateChecker;
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

        foreach (array_keys($postItems) as $ruleId) {
            try {
                /** @var RuleInterface $rule **/
                $rule = $this->ruleRepository->get($ruleId);
                $postData = $postItems[$ruleId];
                if (!$error) {
                    $this->setRuleData($rule, $postData);
                    $this->ruleRepository->save($rule);
                }
            } catch (LocalizedException $e) {
                $messages[] = $this->getErrorWithRuleId($rule, $e->getMessage());
                $error = true;
            } catch (\RuntimeException $e) {
                $messages[] = $this->getErrorWithRuleId($rule, $e->getMessage());
                $error = true;
            } catch (\Exception $e) {
                $messages[] = $this->getErrorWithRuleId(
                    $rule,
                    __('Something went wrong while saving the rule.')
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
     * Retrieve error message with rule id
     *
     * @param RuleInterface $rule
     * @param string $errorText
     * @return string
     */
    private function getErrorWithRuleId(RuleInterface $rule, $errorText)
    {
        return '[Rule ID: ' . $rule->getRuleId() . '] ' . $errorText;
    }

    /**
     * Set rule data
     *
     * @param RuleInterface $rule
     * @param array $ruleData
     * @return $this
     * @throws LocalizedException
     * @throws \Exception
     */
    private function setRuleData(RuleInterface $rule, array $ruleData)
    {
        $originalRule = clone $rule;
        $this->dataObjectHelper->populateWithArray(
            $rule,
            $ruleData,
            RuleInterface::class
        );
        $this->postDataUpdateChecker->checkForReindexNotice($rule, $originalRule);
        return $this;
    }
}
