<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Acr\Block\Adminhtml;

use Aheadworks\Acr\Model\Config;
use Aheadworks\Acr\Api\Data\PreviewInterface;
use Aheadworks\Acr\Api\Data\PreviewInterfaceFactory;
use Magento\Backend\Block\Template\Context;
use Aheadworks\Acr\Model\Preview\Storage;

/**
 * Class Preview
 * @package Aheadworks\Acr\Block\Adminhtml
 */
class Preview extends \Magento\Backend\Block\Template
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var PreviewInterfaceFactory
     */
    private $previewFactory;

    /**
     * @var PreviewInterface
     */
    private $preview;

    /**
     * @var Storage
     */
    private $storage;

    /**
     * @param Context $context
     * @param Config $config
     * @param PreviewInterfaceFactory $previewFactory
     * @param Storage $storage
     * @param array $data
     */
    public function __construct(
        Context $context,
        Config $config,
        PreviewInterfaceFactory $previewFactory,
        Storage $storage,
        array $data = []
    ) {
        $this->config = $config;
        $this->previewFactory = $previewFactory;
        $this->storage = $storage;
        parent::__construct($context, $data);
    }

    /**
     * Get sender name
     *
     * @return string
     */
    public function getSenderName()
    {
        return $this->config->getSenderName($this->getStoreId());
    }

    /**
     * Get sender email
     *
     * @return string
     */
    public function getSenderEmail()
    {
        return $this->config->getSenderEmail($this->getStoreId());
    }

    /**
     * Get recipient name
     *
     * @return string
     */
    public function getRecipientName()
    {
        /** @var PreviewInterface $preview */
        $preview = $this->getPreview();
        return $preview->getRecipientName();
    }

    /**
     * Get recipient email
     *
     * @return string
     */
    public function getRecipientEmail()
    {
        /** @var PreviewInterface $preview */
        $preview = $this->getPreview();
        return $preview->getRecipientEmail();
    }

    /**
     * Get email body
     *
     * @return string
     */
    public function getMessageContent()
    {
        /** @var PreviewInterface $preview */
        $preview = $this->getPreview();
        return $preview->getContent();
    }

    /**
     * Get email subject
     *
     * @return null|string
     */
    public function getMessageSubject()
    {
        /** @var PreviewInterface $preview */
        $preview = $this->getPreview();
        return $preview->getSubject();
    }

    /**
     * Get preview
     *
     * @return PreviewInterface
     */
    private function getPreview()
    {
        if (!$this->preview) {
            if ($this->storage->getPreviewData()) {
                $this->preview = $this->storage->getPreviewData();
            } else {
                $this->preview = $this->previewFactory->create();
            }
        }
        return $this->preview;
    }

    /**
     * Get current store id
     *
     * @return int
     */
    private function getStoreId()
    {
        /** @var PreviewInterface $preview */
        $preview = $this->getPreview();
        return $preview->getStoreId();
    }
}
