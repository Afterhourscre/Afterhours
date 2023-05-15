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



namespace Mirasvit\Seo\Service\Alternate;

class BlogStrategy
{
    private $manager;
    private $registry;
    private $url;

    public function __construct(
        \Magento\Framework\Module\Manager $manager,
        \Magento\Framework\Registry $registry,
        \Mirasvit\Seo\Api\Service\Alternate\UrlInterface $url
    ) {
        $this->manager      = $manager;
        $this->registry     = $registry;
        $this->url          = $url;
    }

    /**
     * {@inheritdoc}
     */
    public function getStoreUrls()
    {
        if (!$this->manager->isEnabled('Mirasvit_Blog')) {
            return [];
        }

        $storeUrls = $this->url->getStoresCurrentUrl();
        $post = $this->registry->registry('current_blog_post');
        $allowedStores = $post->getStoreIds();

        foreach ($storeUrls as $key => $value) {
            if (!in_array($key, $allowedStores)){
                unset($storeUrls[$key]);
            }
        }

        return $storeUrls;
    }
}
