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



namespace Mirasvit\SeoFilter\Plugin\Frontend\Catalog\Model\Layer\Filter\Item;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Model\Layer\Filter\Item;
use Mirasvit\SeoFilter\Model\Config;
use Mirasvit\SeoFilter\Service\FriendlyUrlService;
use Mirasvit\SeoFilter\Service\UrlService;

class AttributeUrlPlugin
{

    /**
     * @var CategoryRepositoryInterface
     */
    private $categoryRepository;

    /**
     * @var FriendlyUrlService
     */
    private $friendlyUrlService;

    /**
     * @var UrlService
     */
    private $urlService;

    /**
     * @var Config
     */
    private $config;

    /**
     * AttributeUrlPlugin constructor.
     * @param CategoryRepositoryInterface $categoryRepository
     * @param FriendlyUrlService $friendlyUrlService
     * @param UrlService $urlService
     * @param Config $config
     */
    public function __construct(
        CategoryRepositoryInterface $categoryRepository,
        FriendlyUrlService $friendlyUrlService,
        UrlService $urlService,
        Config $config
    ) {
        $this->categoryRepository = $categoryRepository;
        $this->friendlyUrlService = $friendlyUrlService;
        $this->urlService         = $urlService;
        $this->config             = $config;
    }

    /**
     * Get filter item url
     *
     * @param Item   $item
     * @param string $url
     *
     * @return string
     */
    public function afterGetUrl(Item $item, $url)
    {
        if (!$this->config->isApplicable()) {
            return $url;
        }

        $itemValue  = $item->getData('value');
        $itemFilter = $item->getFilter();

        if ($item->getFilter()->getRequestVar() == 'cat') {
            $categoryUrl = $this->categoryRepository->get($itemValue)
                ->getUrl();

            return $this->urlService->addUrlParams($categoryUrl);
        }

        if (empty($itemFilter)) {
            return $url;
        }

        $attributeCode = $itemFilter->getRequestVar();

        if (!$attributeCode) {
            return $url;
        }

        $url = $this->friendlyUrlService->getUrl($attributeCode, $itemValue);

        return $this->urlService->addUrlParams($url);
    }

    /**
     * Get url for remove item from filter
     *
     * @param Item   $item
     * @param string $url
     *
     * @return string
     */
    public function afterGetRemoveUrl(Item $item, $url)
    {
        if (!$this->config->isApplicable()) {
            return $url;
        }

        $itemFilter = $item->getFilter();
        $itemValue  = $item->getData('value');

        if (empty($itemFilter)) {
            return $url;
        }

        $attributeCode = $itemFilter->getRequestVar();

        if (!$attributeCode || !$itemValue) {
            return $url;
        }

        $url = $this->friendlyUrlService->getUrl($attributeCode, $itemValue, true);

        return $this->urlService->addUrlParams($url);
    }
}
