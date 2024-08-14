<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */


namespace Aheadworks\Faq\Controller;

use Aheadworks\Faq\Model\Url;
use Aheadworks\Faq\Model\ResourceModel\Category as ResourceCategory;
use Aheadworks\Faq\Model\ResourceModel\Article as ResourceArticle;
use Magento\Framework\App\State;
use Magento\Framework\App\Action\Forward;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\RouterInterface;
use Magento\Framework\App\ActionFactory;

/**
 * FAQ Controller Router
 */
class Router implements RouterInterface
{
    /**
     * @var ActionFactory
     */
    private $actionFactory;

    /**
     * Article resource model
     *
     * @var ResourceArticle
     */
    private $resourceArticle;

    /**
     * Category resource model
     *
     * @var ResourceCategory
     */
    private $resourceCategory;

    /**
     * @var Url
     */
    private $url;

    /**
     * @param Url $url
     * @param ActionFactory $actionFactory
     * @param ResourceArticle $resourceArticle
     * @param ResourceCategory $resourceCategory
     */
    public function __construct(
        Url $url,
        ActionFactory $actionFactory,
        ResourceArticle $resourceArticle,
        ResourceCategory $resourceCategory
    ) {
        $this->url = $url;
        $this->actionFactory = $actionFactory;
        $this->resourceArticle = $resourceArticle;
        $this->resourceCategory = $resourceCategory;
    }

    /**
     * Validate and Match FAQ Pages and modify request
     *
     * @param RequestInterface|\Magento\Framework\App\Request\Http $request
     * @return bool
     */
    public function match(RequestInterface $request)
    {
        $path = explode('/', trim($request->getPathInfo(), '/'));
        $faqRoute = $this->url->getFaqRoute();

        if ($path[0] != $faqRoute) {
            return null;
        }

        if (isset($path[2])) {
            $articleId = $this->resourceArticle->getIdByUrlKey($path[2]);
            if ($articleId) {
                $request
                    ->setModuleName('faq')
                    ->setControllerName('article')
                    ->setActionName('index')
                    ->setParam('id', $articleId);
            } else {
                return null;
            }
        } elseif (isset($path[1])) {
            if ($path[1] == Url::FAQ_SEARCH_ROUTE) {
                $request
                    ->setModuleName('faq')
                    ->setControllerName('search')
                    ->setActionName('index');
            } else {
                $categoryId = $this->resourceCategory->getIdByUrlKey($path[1]);
                if ($categoryId) {
                    $request
                        ->setModuleName('faq')
                        ->setControllerName('category')
                        ->setActionName('index')
                        ->setParam('id', $categoryId);
                } else {
                    return null;
                }
            }
        } else {
            $request
                ->setModuleName('faq')
                ->setControllerName('index')
                ->setActionName('index');
        }

        return $this->actionFactory->create(Forward::class, ['request' => $request]);
    }
}
