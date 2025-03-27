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


declare(strict_types=1);

namespace Mirasvit\Seo\Service\TemplateEngine\Data;

use Magento\Framework\Module\Manager as ModuleManager;
use Magento\Store\Model\StoreManagerInterface;
use Mirasvit\Seo\Api\Service\StateServiceInterface;

class BrandData extends AbstractData
{
    private $storeManager;

    private $stateService;

    private $moduleManager;

    public function __construct(
        StoreManagerInterface $storeManager,
        StateServiceInterface $stateService,
        ModuleManager         $moduleManager
    ) {
        $this->storeManager  = $storeManager;
        $this->stateService  = $stateService;
        $this->moduleManager = $moduleManager;

        parent::__construct();
    }

    public function getTitle(): string
    {
        return (string)__('Brand Data');
    }

    public function getVariables(): array
    {
        if (!$this->moduleManager->isEnabled('Mirasvit_Brand')) {
            return [];
        }

        return [
            'name',
            'meta_title',
            'meta_keywords',
            'meta_description',
        ];
    }

    public function getValue(string $attribute, array $additionalData = []): ?string
    {
        if ($brandPage = $this->stateService->getBrandPage()) {
            $storeId = (int)$this->storeManager->getStore()->getId();
            $value   = null;

            switch ($attribute) {
                case 'name':
                    $value = $brandPage->getDataFromGroupedField(\Mirasvit\Brand\Model\BrandPage::BRAND_TITLE, 'content', $storeId);
                    break;
                case 'meta_title':
                    $value = $brandPage->getDataFromGroupedField(\Mirasvit\Brand\Model\BrandPage::META_TITLE, 'meta_data', $storeId);
                    break;
                case 'meta_keywords':
                    $value = $brandPage->getDataFromGroupedField(\Mirasvit\Brand\Model\BrandPage::KEYWORD, 'meta_data', $storeId);
                    break;
                case 'meta_description':
                    $value = $brandPage->getDataFromGroupedField(\Mirasvit\Brand\Model\BrandPage::META_DESCRIPTION, 'meta_data', $storeId);
                    break;
            }

            return $value;
        } else {
            return null;
        }
    }
}
