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
 * @package   mirasvit/module-seo
 * @version   2.0.154
 * @copyright Copyright (C) 2019 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\SeoAutolink\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Mirasvit\SeoAutolink\Model\Config\Source\Target;

class AddCustomAttributeOutputObserver implements ObserverInterface
{
    /**
     * @var \Mirasvit\SeoAutolink\Model\Config
     */
    protected $config;

    /**
     * @var \Mirasvit\SeoAutolink\Helper\Replace
     */
    protected $dataHelper;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Framework\Model\Context
     */
    protected $context;

    /**
     * @var \Magento\Catalog\Helper\Output
     */
    protected $catalogOutputHelper;

    public function __construct(
        \Mirasvit\SeoAutolink\Model\Config $config,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Model\Context $context,
        \Magento\Catalog\Helper\Output $catalogOutputHelper
    ) {
        $this->config              = $config;
        $this->dataHelper          = $objectManager->get('\Mirasvit\SeoAutolink\Helper\Replace');
        $this->registry            = $registry;
        $this->context             = $context;
        $this->catalogOutputHelper = $catalogOutputHelper;
    }

    /**
     * @param string $outputHelper
     * @param string $outputHtml
     * @param string $params
     *
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function categoryAttribute($outputHelper, $outputHtml, $params)
    {
        if (!$this->registry->registry('current_category')) {
            return $outputHtml;
        }
        if ($params['attribute'] == 'description' &&
            in_array(
                Target::CATEGORY_DESCRIPTION,
                $this->config->getTarget()
            )
        ) {
            $outputHtml = $this->dataHelper->addLinks($outputHtml);
        }

        return $outputHtml;
    }

    /**
     * @param object $outputHelper
     * @param string $outputHtml
     * @param array  $params
     *
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function productAttribute($outputHelper, $outputHtml, $params)
    {
        if (!$this->registry->registry('current_product')) {
            return $outputHtml;
        }

        switch ($params['attribute']) {
            case 'short_description':
                if ($this->config->isAllowedTarget(Target::PRODUCT_SHORT_DESCRIPTION)) {

                    $outputHtml = $this->dataHelper->addLinks($outputHtml);
                }

                break;

            case 'description':
                if ($this->config->isAllowedTarget(Target::PRODUCT_FULL_DESCRIPTION)) {
                    $outputHtml = $this->dataHelper->addLinks($outputHtml);
                }
                break;

            default:
                if ($this->config->isAllowedTarget(Target::PRODUCT_ATTRIBUTE)) {
                    $outputHtml = $this->dataHelper->addLinks($outputHtml);
                }
        }

        return $outputHtml;
    }

    /**
     * @param Observer $observer
     *
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute(Observer $observer)
    {
        $this->catalogOutputHelper->addHandler('productAttribute', $this);
        $this->catalogOutputHelper->addHandler('categoryAttribute', $this);
    }
}
