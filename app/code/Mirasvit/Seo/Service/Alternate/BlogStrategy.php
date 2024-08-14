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

namespace Mirasvit\Seo\Service\Alternate;

use Magento\Framework\App\ObjectManager;

class BlogStrategy implements \Mirasvit\Seo\Api\Service\Alternate\StrategyInterface
{
    private $manager;

    private $registry;

    private $url;

    public function __construct(
        \Magento\Framework\Module\Manager $manager,
        \Magento\Framework\Registry $registry,
        \Mirasvit\Seo\Api\Service\Alternate\UrlInterface $url
    ) {
        $this->manager  = $manager;
        $this->registry = $registry;
        $this->url      = $url;
    }

    /**
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function getStoreUrls(): array
    {
        if (!$this->manager->isEnabled('Mirasvit_BlogMx')) {
            return [];
        }

        $storeUrls = $this->url->getStoresCurrentUrl();

        if (!$storeUrls) {
            return [];
        }

        $blogRegistry = null;

        if (class_exists('\Mirasvit\BlogMx\Registry')) {
            $blogRegistry = ObjectManager::getInstance()->get('\Mirasvit\BlogMx\Registry');
        }

        if (!$blogRegistry) {
            return [];
        }

        $entity = null;

        if ($post = $blogRegistry->getPost()) {
            $entity = $post;
        } elseif ($category = $blogRegistry->getCategory()) {
            $entity = $category;
        } else {
            return [];
        }

        $allowedStores = $entity->getStoreIds();

        if (empty($allowedStores)) {
            return $storeUrls;
        }

        foreach ($storeUrls as $key => $value) {
            if (!in_array($key, $allowedStores)) {
                unset($storeUrls[$key]);
            }
        }

        return $storeUrls;
    }

    public function getAlternateUrl(array $storeUrls): array
    {
        return [];
    }
}
