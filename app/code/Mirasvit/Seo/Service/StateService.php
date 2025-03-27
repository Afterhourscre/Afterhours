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



namespace Mirasvit\Seo\Service;

use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Catalog\Model\Layer\Resolver as LayerResolver;
use Magento\Cms\Api\Data\PageInterface;
use Magento\Cms\Api\PageRepositoryInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Module\Manager;
use Magento\Framework\Registry;
use Mirasvit\Seo\Api\Service\StateServiceInterface;

class StateService implements StateServiceInterface
{
    private $registry;

    private $request;

    private $layerResolver;

    private $pageRepository;

    private $moduleManager;

    private $objectManager;

    public function __construct(
        Registry $registry,
        RequestInterface $request,
        LayerResolver $layerResolver,
        PageRepositoryInterface $pageRepository,
        Manager $moduleManager,
        ObjectManagerInterface $objectManager
    ) {
        $this->registry       = $registry;
        $this->request        = $request;
        $this->layerResolver  = $layerResolver;
        $this->pageRepository = $pageRepository;
        $this->moduleManager  = $moduleManager;
        $this->objectManager  = $objectManager;
    }

    /**
     * @return CategoryInterface|null
     */
    public function getCategory()
    {
        $category = $this->registry->registry('current_category');

        return $category && $category instanceof CategoryInterface
            ? $category
            : null;
    }

    /**
     * @return false|\Magento\Catalog\Model\Product|mixed
     */
    public function getProduct()
    {
        return $this->registry->registry('current_product');
    }

    /**
     * @return PageInterface|null
     * @throws LocalizedException
     */
    public function getCmsPage(): ?PageInterface
    {
        $page   = null;
        $pageId = $this->request->getParam('page_id');

        if ($this->isCmsPage() && !empty($pageId)) {
            $page = $this->pageRepository->getById($pageId);
        }

        return $page;
    }

    /**
     * @return bool|false|\Magento\Catalog\Model\Category|\Magento\Framework\DataObject|mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getFilters()
    {
        if (!$this->isNavigationPage() || !$this->getCategory()) {
            return null;
        }

        $category = $this->getCategory();

        $filters        = $this->layerResolver->get()->getState()->getFilters();
        $objectManager  = \Magento\Framework\App\ObjectManager::getInstance();
        $poductResource = $objectManager->get('Magento\Catalog\Model\ResourceModel\ProductFactory')->create();

        $filtersData = [];

        foreach ($filters as $filter) {
            if (method_exists($filter, 'getValueString') && $filter->getFilter()->getRequestVar() !== null) {

                $filtersData[$filter->getFilter()->getRequestVar()][] = $filter->getValueString();
            } else {
                /** @var mixed $filter */
                if ($filter->isApplied()) {
                    $attribute = $poductResource->getAttribute($filter->getFilter()->getData('param_name'));
                    foreach ($filter->getAppliedOptions() as $key => $value) {
                        if ($attribute->usesSource()) {
                            $optionText = $attribute->getSource()->getOptionText($value);
                        } else {
                            $optionText = $value;
                        }

                        $filtersData[$filter->getFilter()->getData('param_name')][] = $optionText;
                    }
                }
            }
        }

        // compatibility with multiselect in navigation
        foreach ($filtersData as $code => $data) {
            try {
                $category->setData($code, implode(',', $data));
            } catch (\Exception $e) {
            }
        }

        return $category;
    }

    public function isCategoryPage(): bool
    {
        return $this->getCategory() && $this->request->getFullActionName() == 'catalog_category_view';
    }

    public function isNavigationPage(): bool
    {
        try {
            $filters = $this->layerResolver->get()->getState()->getFilters();
        } catch (\Exception $e) {
            return false;
        }

        return $this->isCategoryPage() && count($filters) > 0;
    }

    public function isProductPage(): bool
    {
        return $this->getProduct() && $this->request->getFullActionName() == 'catalog_product_view';
    }

    public function isCmsPage(): bool
    {
        return $this->isHomePage() || $this->request->getFullActionName() == 'cms_page_view';
    }

    public function isHomePage(): bool
    {
        if ($this->request->getFullActionName() == 'cms_index_index') {
            return true;
        }

        return false;
    }

    public function isBlogPage(): bool
    {
        if ($this->moduleManager->isEnabled('Mirasvit_BlogMx') && $this->request->getRouteName() === 'blog') {
            return true;
        }

        return false;
    }

    public function isBrandPage(): bool
    {
        if ($this->moduleManager->isEnabled('Mirasvit_Brand') && $this->request->getRouteName() === 'brand') {
            return true;
        }

        return false;
    }

    public function isAllBrandsPage(): bool
    {
        if ($this->isBrandPage() && $this->request->getFullActionName() === 'brand_brand_index') {
            return true;
        }

        return false;
    }

    public function getBlogPage()
    {
        $blogPage = null;

        if ($this->isBlogPage()) {
            switch ($this->request->getFullActionName()) {
                case 'blog_author_view':
                    $authorRepository = $this->objectManager->create('Mirasvit\BlogMx\Repository\AuthorRepository');
                    if ($id = (int)$this->request->getParam('id')) {
                        $blogPage = $authorRepository->get($id);
                    }
                    break;
                case 'blog_category_view':
                    $categoryRepository = $this->objectManager->create('Mirasvit\BlogMx\Repository\CategoryRepository');
                    if ($id = (int)$this->request->getParam('category_id')) {
                        $blogPage = $categoryRepository->get($id);
                    }
                    break;
                case 'blog_home_index':
                    $categoryRepository = $this->objectManager->create('Mirasvit\BlogMx\Repository\CategoryRepository');
                    $blogPage           = $categoryRepository->getRootCategory();
                    $blogConfig         = $this->objectManager->create('\Mirasvit\BlogMx\Model\ConfigProvider');

                    $blogPage->addData([
                        'name' => $blogConfig->getBlogName(),
                        'meta_title' => $blogConfig->getBaseMetaTitle(),
                        'meta_keyword' => $blogConfig->getBaseMetaKeywords(),
                        'meta_description' => $blogConfig->getBaseMetaDescription()
                    ]);

                    break;
                case 'blog_post_view':
                    $postRepository = $this->objectManager->create('Mirasvit\BlogMx\Repository\PostRepository');
                    if ($id = (int)$this->request->getParam('post_id')) {
                        $blogPage = $postRepository->get($id);
                    }
                    break;
                case 'blog_tag_view':
                    $tagRepository = $this->objectManager->create('Mirasvit\BlogMx\Repository\TagRepository');
                    if ($id = (int)$this->request->getParam('id')) {
                        $blogPage = $tagRepository->get($id);
                    }
                    break;
            }
        }

        return $blogPage;
    }

    public function getBrandPage()
    {
        $brandPage = null;

        if ($this->isBrandPage()) {
            switch ($this->request->getFullActionName()) {
                case 'brand_brand_view':
                    $brandRepository = $this->objectManager->create('Mirasvit\Brand\Repository\BrandRepository');

                    if ($id = $this->request->getParam('attribute_option_id')) {
                        $brand     = $brandRepository->get((int)$id);
                        $brandPage = $brand ? $brand->getPage() : null;
                    }

                    break;
                case 'brand_brand_index':
                    $brandPage          = $this->objectManager->create('\Mirasvit\Brand\Model\BrandPage');
                    $allBrandPageConfig = $this->objectManager->create('\Mirasvit\Brand\Model\Config\AllBrandPageConfig');

                    $brandPage->setMetaData([[
                        'meta_title' => $allBrandPageConfig->getMetaTitle(),
                        'meta_keyword' => $allBrandPageConfig->getMetaKeyword(),
                        'meta_description' => $allBrandPageConfig->getMetaDescription()
                    ]]);

                    $brandPage->setContent([[
                        'brand_title' => ' '
                    ]]);

                    break;
            }
        }

        return $brandPage;
    }
}
