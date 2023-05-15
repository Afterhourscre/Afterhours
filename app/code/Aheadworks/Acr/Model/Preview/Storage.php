<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Acr\Model\Preview;

use Magento\Framework\Session\StorageInterface;
use Magento\Framework\Registry;

/**
 * Class Storage
 * @package Aheadworks\Acr\Model\Preview
 */
class Storage
{
    /**
     * {@inheritdoc}
     */
    const REGISTRY_NAME = 'aw_acr_preview';

    /**
     * {@inheritdoc}
     */
    const SESSION_NAME = 'aw_acr_preview_data';

    /**
     * @var StorageInterface
     */
    private $sessionStorage;

    /**
     * @var Registry
     */
    private $coreRegistry;

    /**
     * @param StorageInterface $sessionStorage
     * @param Registry $coreRegistry
     */
    public function __construct(
        StorageInterface $sessionStorage,
        Registry $coreRegistry
    ) {
        $this->sessionStorage = $sessionStorage;
        $this->coreRegistry = $coreRegistry;
    }

    /**
     * Save email data
     *
     * @param array $data
     */
    public function saveEmailData($data)
    {
        $this->sessionStorage->setData(self::SESSION_NAME, $data);
    }

    /**
     * Get email data
     *
     * @return array
     */
    public function getEmailData()
    {
        return $this->sessionStorage->getData(self::SESSION_NAME);
    }

    /**
     * Save preview data
     *
     * @param array $data
     */
    public function savePreviewData($data)
    {
        try {
            $this->coreRegistry->register(self::REGISTRY_NAME, $data);
        } catch (\RuntimeException $e) {
            $this->coreRegistry->unregister(self::REGISTRY_NAME);
            $this->coreRegistry->register(self::REGISTRY_NAME, $data);
        }
    }

    /**
     * Get preview data
     *
     * @return array
     */
    public function getPreviewData()
    {
        return $this->coreRegistry->registry(self::REGISTRY_NAME);
    }
}
