<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Acr\Controller\Adminhtml\Rule;

use Aheadworks\Acr\Model\Config;
use Aheadworks\Acr\Api\Data\PreviewInterface;
use Aheadworks\Acr\Api\RuleManagementInterface;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\Data\Form\FormKey;
use Magento\Store\Model\Store;
use Aheadworks\Acr\Model\Preview\Storage;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Preview
 * @package Aheadworks\Acr\Controller\Adminhtml\Rule
 */
class Preview extends \Magento\Backend\App\Action
{
    /**
     * {@inheritdoc}
     */
    const ADMIN_RESOURCE = 'Aheadworks_Acr::rules';

    /**
     * @var Config
     */
    private $config;

    /**
     * @var Registry
     */
    private $coreRegistry;

    /**
     * @var RuleManagementInterface
     */
    private $ruleManagement;

    /**
     * @var FormKey
     */
    private $formKey;

    /**
     * @var Storage
     */
    protected $storage;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param Context $context
     * @param Config $config
     * @param Registry $coreRegistry
     * @param RuleManagementInterface $ruleManagement
     * @param FormKey $formKey
     * @param Storage $storage
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Context $context,
        Config $config,
        Registry $coreRegistry,
        RuleManagementInterface $ruleManagement,
        FormKey $formKey,
        Storage $storage,
        StoreManagerInterface $storeManager
    ) {
        parent::__construct($context);
        $this->config = $config;
        $this->coreRegistry = $coreRegistry;
        $this->ruleManagement = $ruleManagement;
        $this->formKey = $formKey;
        $this->storage = $storage;
        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $data = $this->storage->getEmailData();
        if (empty($data)) {
            $this->_forward('noroute');
        } else {
            /** @var PreviewInterface $preview */
            $preview = $this->getPreview($data);
            $this->storage->savePreviewData($preview);

            $this->_view->loadLayout(['aw_acr_preview'], true, true, false);
            $this->_view->renderLayout();
        }
    }

    /**
     * Get preview data
     *
     * @param array $emailData
     * @return PreviewInterface
     */
    private function getPreview($emailData)
    {
        $subject = isset($emailData['subject']) ? $emailData['subject'] : '';
        $content = isset($emailData['content']) ? $emailData['content'] : '';
        $storeId = Store::DEFAULT_STORE_ID;
        if (isset($emailData['store_ids'])) {
            if (count($emailData['store_ids']) > 0) {
                $storeId = array_shift($emailData['store_ids']);
            }
        }
        if ($storeId == Store::DEFAULT_STORE_ID) {
            $storeId = $this->storeManager->getDefaultStoreView()->getId();
        }
        /** @var PreviewInterface $preview */
        $preview = $this->ruleManagement->getPreview($storeId, $subject, $content);
        return $preview;
    }
}
