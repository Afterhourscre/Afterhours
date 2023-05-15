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



namespace Mirasvit\Seo\Service\TemplateEngine\Data;

use Magento\Cms\Model\Page;

class CmsPageData extends AbstractData
{
    private $page;

    public function __construct(
        Page $page
    ) {
        $this->page = $page;

        parent::__construct();
    }

    public function getTitle()
    {
        return __('CMS Page Data');
    }

    public function getVariables()
    {
        return [
            'title',
            'meta_keywords',
            'meta_description',
            'content_heading',
            'content',
            'meta_title',
            'apply_for_homepage',
        ];
    }

    public function getValue($attribute, $additionalData = [])
    {
        if (!$this->page->getIdentifier()) {
            return false;
        }

        return $this->page->getDataUsingMethod($attribute);
    }
}
