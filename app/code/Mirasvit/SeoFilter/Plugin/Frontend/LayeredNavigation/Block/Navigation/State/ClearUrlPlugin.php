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
 * @package   mirasvit/module-seo-filter
 * @version   1.0.16
 * @copyright Copyright (C) 2020 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\SeoFilter\Plugin\Frontend\LayeredNavigation\Block\Navigation\State;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Mirasvit\SeoFilter\Model\Config;
use Mirasvit\SeoFilter\Model\Context;

class ClearUrlPlugin
{
    /**
     * @var CategoryRepositoryInterface
     */
    private $categoryRepository;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var Context
     */
    private $context;

    /**
     * ClearUrlPlugin constructor.
     * @param CategoryRepositoryInterface $categoryRepository
     * @param Config $config
     * @param Context $context
     */
    public function __construct(
        CategoryRepositoryInterface $categoryRepository,
        Config $config,
        Context $context
    ) {
        $this->categoryRepository = $categoryRepository;
        $this->config             = $config;
        $this->context            = $context;
    }

    /**
     * @param \Magento\LayeredNavigation\Block\Navigation\State $subject
     * @param string                                            $result
     *
     * @return string
     */
    public function afterGetClearUrl($subject, $result)
    {
        if (!$this->config->isApplicable()) {
            return $result;
        }

        $category = $this->categoryRepository->get($this->context->getCurrentCategory()->getId());

        return $category->getUrl();
    }
}
