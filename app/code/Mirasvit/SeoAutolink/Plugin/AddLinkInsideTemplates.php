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
 * @version   2.0.169
 * @copyright Copyright (C) 2020 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\SeoAutolink\Plugin;

use Magento\Framework\View\TemplateEngineFactory;
use Magento\Framework\View\TemplateEngineInterface;
use Magento\Store\Model\StoreManagerInterface;
use Mirasvit\SeoAutolink\Api\Config\AutolinkInterface;
use Mirasvit\SeoAutolink\Service\AddLinks\AddLinksFactory;
use Mirasvit\SeoAutolink\Service\TextProcessorService;


class AddLinkInsideTemplates
{
    /**
     * @var AutolinkInterface
     */
    protected $autolinkConfig;

    /**
     * @var AddLinksFactory
     */
    protected $addLinks;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    protected $textProcessorService;

    public function __construct(
        AutolinkInterface $autolinkConfig,
        AddLinksFactory $addLinks,
        StoreManagerInterface $storeManager,
        TextProcessorService $textProcessorService
    ) {
        $this->autolinkConfig       = $autolinkConfig;
        $this->addLinks             = $addLinks;
        $this->storeManager         = $storeManager;
        $this->textProcessorService = $textProcessorService;
    }

    /**
     * Add autolinks in templates depending of the SEOAutolink configuration
     *
     * @param TemplateEngineFactory   $subject
     * @param TemplateEngineInterface $invocationResult
     *
     * @return TemplateEngineInterface
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterCreate(
        TemplateEngineFactory $subject,
        TemplateEngineInterface $invocationResult
    ) {
        \Magento\Framework\Profiler::start(__METHOD__);
        $store     = $this->storeManager->getStore();
        $templates = $this->autolinkConfig->getTemplates($store);

        if ($templates) {
            $res = $this->addLinks->create([
                'subject'       => $invocationResult,
                'templates'     => $templates,
                'replaceHelper' => $this->textProcessorService,
            ]);
            \Magento\Framework\Profiler::stop(__METHOD__);

            return $res;
        }
        \Magento\Framework\Profiler::stop(__METHOD__);

        return $invocationResult;
    }
}
