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
 * @version   2.9.6
 * @copyright Copyright (C) 2024 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\SeoContent\Ui\Template\Source;

use Magento\Framework\Module\Manager;
use Magento\Framework\Option\ArrayInterface;
use Mirasvit\SeoContent\Api\Data\TemplateInterface;

class RuleTypeSource implements ArrayInterface
{
    private $moduleManager;

    public function __construct(
        Manager $moduleManager
    ) {
        $this->moduleManager = $moduleManager;
    }

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        $optionArray = [
            [
                'label' => __('Categories'),
                'value' => TemplateInterface::RULE_TYPE_CATEGORY,
            ],
            [
                'label' => __('Layered Navigation'),
                'value' => TemplateInterface::RULE_TYPE_NAVIGATION,
            ],
            [
                'label' => __('Products'),
                'value' => TemplateInterface::RULE_TYPE_PRODUCT,
            ],
            [
                'label' => __('CMS Pages'),
                'value' => TemplateInterface::RULE_TYPE_PAGE,
            ],
        ];

        if ($this->moduleManager->isEnabled('Mirasvit_BlogMx')) {
            $optionArray[] = [
                'label' => __('Blog'),
                'value' => TemplateInterface::RULE_TYPE_BLOG,
            ];
        }

        if ($this->moduleManager->isEnabled('Mirasvit_Brand')) {
            $optionArray[] = [
                'label' => __('Brand'),
                'value' => TemplateInterface::RULE_TYPE_BRAND,
            ];
        }

        return $optionArray;
    }
}
