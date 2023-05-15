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



namespace Mirasvit\SeoContent\Plugin\Frontend\Framework\View\TemplateEngine;

use Mirasvit\SeoContent\Api\Data\ContentInterface;
use Mirasvit\SeoContent\Service\ContentService;

/**
 * Purpose: Add Seo Description after specified template
 */
class AddSeoDescriptionPlugin
{
    private $contentService;

    private $level     = 0;

    private $templates = [];

    public function __construct(
        ContentService $contentService
    ) {
        $this->contentService = $contentService;
    }

    /**
     * @param \Magento\Framework\View\TemplateEngine\Php $subject
     * @param object                                     $block
     * @param string                                     $template
     * @return  \Magento\Framework\View\TemplateEngine\Php
     */
    public function beforeRender($subject, $block = null, $template = null)
    {
        $this->templates[$this->level] = $template;
        $this->level++;

        return null;
    }

    /**
     * @param \Magento\Framework\View\TemplateEngine\Php $subject
     * @param string                                     $result
     * @return string
     */
    public function afterRender($subject, $result)
    {
        $this->level--;
        $template = $this->templates[$this->level];

        $content = $this->contentService->getCurrentContent();

        if ($content->getDescriptionPosition() == ContentInterface::DESCRIPTION_POSITION_CUSTOM_TEMPLATE
            && strpos($template, $content->getDescriptionTemplate()) !== false) {

            $result .= $content->getDescription();
        }

        return $result;
    }
}
