<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-review
 * @version   1.1.2
 * @copyright Copyright (C) 2024 Mirasvit (https://mirasvit.com/)
 */


declare(strict_types=1);


namespace Mirasvit\Review\Block\Product\Review;


use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\Element\Template;
use Mirasvit\Review\Model\ConfigProvider;

class Uploader extends \Magento\Framework\View\Element\Template
{
    private $scopeConfig;

    private $configProvider;

    public function __construct(
        ConfigProvider $configProvider,
        ScopeConfigInterface $scopeConfig,
        Template\Context $context,
        array $data = []
    ) {
        $this->configProvider = $configProvider;
        $this->scopeConfig = $scopeConfig;

        parent::__construct($context, $data);
    }

    public function getResizeConfig(): array
    {
        $config = ['action' => 'resizeImage'];

        if (!$this->scopeConfig->getValue('system/upload_configuration/enable_resize')) {
            return $config;
        }

        $maxWidth = $this->scopeConfig->getValue('system/upload_configuration/max_width') ?: 1920;
        $maxHeight = $this->scopeConfig->getValue('system/upload_configuration/enable_resize') ?: 1200;

        $config['maxWidth']  = $maxWidth;
        $config['maxHeight'] = $maxWidth;

        return $config;
    }

    public function getMaxNumFiles()
    {
        return $this->configProvider->getMaxNumberFiles();
    }

    public function displayReviewPhoto()
    {
        return $this->configProvider->isMediaEnabled();
    }

    public function displayReviewPhotoGallery()
    {
        return $this->configProvider->displayReviewPhotoGallery();
    }
}
