<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Acr\Model\Email;

use Aheadworks\Acr\Model\Config;
use Aheadworks\Acr\Model\Email\Template\Content as TemplateContent;

/**
 * Class Content
 * @package Aheadworks\Acr\Model\Email
 */
class Content
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var TemplateContent
     */
    private $templateContent;

    /**
     * @param Config $config
     * @param TemplateContent $templateContent
     */
    public function __construct(
        Config $config,
        TemplateContent $templateContent
    ) {
        $this->config = $config;
        $this->templateContent = $templateContent;
    }

    /**
     * Get Full Content
     *
     * @param string $content
     * @param int $storeId
     * @return string
     */
    public function getFullContent($content, $storeId)
    {
        $header = $this->templateContent->getTemplateContent($this->config->getHeaderTemplateId($storeId), $storeId);
        $footer = $this->templateContent->getTemplateContent($this->config->getFooterTemplateId($storeId), $storeId);
        return $header . $content . $footer;
    }
}
