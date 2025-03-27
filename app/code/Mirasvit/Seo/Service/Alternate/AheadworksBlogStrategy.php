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


use Magento\Framework\App\RequestInterface;
use Magento\Framework\Module\Manager;
use Magento\Framework\App\ObjectManager;
use Mirasvit\Seo\Api\Service\Alternate\UrlInterface;

class AheadworksBlogStrategy implements \Mirasvit\Seo\Api\Service\Alternate\StrategyInterface
{
    private $manager;

    private $url;

    protected $request;

    public function __construct(
        Manager $manager,
        UrlInterface $url,
        RequestInterface $request
    ) {
        $this->manager = $manager;
        $this->url     = $url;
        $this->request = $request;
    }

    /**
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function getStoreUrls(): array
    {
        if (!$this->manager->isEnabled('Aheadworks_Blog')) {
            return [];
        }

        $allowedStores = [];

        $entityType = null;
        $entityId   = null;

        if ($id = $this->request->getParam('post_id')) {
            $entityId   = $id;
            $entityType = 'Post';
        } elseif ($id = $this->request->getParam('blog_category_id')) {
            $entityId   = $id;
            $entityType = 'Category';
        }

        if (!$entityId && !$entityType) {
            return [];
        }

        $repository = ObjectManager::getInstance()->get("\Aheadworks\Blog\Api\\" . $entityType . "RepositoryInterface");

        if (!$repository) {
            return [];
        }

        $entity = $repository->get($entityId);

        if (!$entity) {
            return [];
        }

        $allowedStores = $entity->getStoreIds();
        $storeUrls     = $this->url->getStoresCurrentUrl();

        if (!$storeUrls) {
            return [];
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
