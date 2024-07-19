<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace MGS\Portfolio\Block;

use Magento\Framework\View\Element\Template;

/**
 * Main contact form block
 */
class Category extends Template
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var \Magento\Framework\View\Page\Config
     */
    protected $pageConfig;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @param Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\View\Page\Config $pageConfig,
        Template\Context $context, array $data = []
    )
    {
        $this->_objectManager = $objectManager;
        $this->pageConfig = $pageConfig;
        $this->_storeManager = $storeManager;
        parent::__construct($context, $data);
    }

    /**
     * Prepare global layout
     *
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareLayout()
    {

        parent::_prepareLayout();

        $title = __('Portfolio List');

        if ($id = $this->getRequest()->getParam('id')) {
            $category = $this->getModel()->load($id);
            $title = $category->getCategoryName();
        }

        $breadcrumbsBlock = $this->getLayout()->getBlock('breadcrumbs');
        $breadcrumbsBlock->addCrumb(
            'home',
            [
                'label' => __('Home'),
                'title' => __('Go to Home Page'),
                'link' => $this->_storeManager->getStore()->getBaseUrl()
            ]
        );

        $breadcrumbsBlock->addCrumb('portfolio_category', ['label' => $title, 'title' => $title]);

        $this->pageConfig->getTitle()->set($title);

        if ($this->getPortfolios()) {
            $pager = null;
            $this->getPortfolios()->load();
        }

        return $this;
    }

    public function getModel()
    {
        return $this->_objectManager->create('MGS\Portfolio\Model\Category');
    }

    public function getPortfolios()
    {
        //get values of current page. if not the param value then it will set to 1
        $page = ($this->getRequest()->getParam('p')) ? $this->getRequest()->getParam('p') : 1;
        //get values of current limit. if not the param value then it will set to 24
        $pageSize = ($this->getRequest()->getParam('limit')) ? $this->getRequest()->getParam('limit') : 24;

        $portfolios = $this->_objectManager->create('MGS\Portfolio\Model\Portfolio')
            ->getCollection()
            ->addFieldToFilter('status', 1);

        if ($id = $this->getRequest()->getParam('id')) {
            $resourceModel = $this->_objectManager->create('MGS\Portfolio\Model\ResourceModel\Portfolio');
            $portfolios = $resourceModel->joinFilter($portfolios, $id);
        }

        /* foreach ($portfolios as $portfolio) {
            $portfolio->setAddress($this->getUrl($this->helper('portfolio')->getPortfolioUrl($portfolio)));
        } */
        $portfolios->setPageSize($pageSize);
        $portfolios->setCurPage($page);
        return $portfolios;
    }

    public function getPortfolioAddress($portfolio)
    {
        $identifier = $portfolio->getIdentifier();
        if ($identifier != '') {
            return $this->getUrl('portfolio/' . $identifier);
        }
        return $this->getUrl('portfolio/index/view', ['id' => $portfolio->getId()]);
    }

    public function getThumbnailSrc($portfolio)
    {
        $filePath = 'mgs/portfolio/thumbnail/' . $portfolio->getThumbnailImage();
        if ($filePath != '') {
            $thumbnailUrl = $this->_urlBuilder->getBaseUrl(['_type' => \Magento\Framework\UrlInterface::URL_TYPE_MEDIA]) . $filePath;
            return $thumbnailUrl;
        }
        return 0;
    }

    public function getCategories($portfolio)
    {

        //get values of current page. if not the param value then it will set to 1
        $page = ($this->getRequest()->getParam('p')) ? $this->getRequest()->getParam('p') : 1;
        //get values of current limit. if not the param value then it will set to 1
        $pageSize = ($this->getRequest()->getParam('limit')) ? $this->getRequest()->getParam('limit') : 5;

        $collection = $this->_objectManager->create('MGS\Portfolio\Model\Stores')
            ->getCollection()
            ->addFieldToFilter('portfolio_id', $portfolio->getId());
        $collection->setPageSize($pageSize);
        $collection->setCurPage($page);

        $resourceModel = $this->_objectManager->create('MGS\Portfolio\Model\ResourceModel\Stores');
        $collection = $resourceModel->joinFilter($collection);
        return $collection;
    }

    public function getCategoriesText($portfolio)
    {
        $collection = $this->getCategories($portfolio);

        if (count($collection) > 0) {
            $arrResult = [];
            foreach ($collection as $item) {
                $arrResult[] = $item->getName();
            }
            return implode(', ', $arrResult);
        }
        return '';
    }

    public function getCategoriesLink($portfolio)
    {
        $collection = $this->getCategories($portfolio);
        $html = '';
        if (count($collection) > 0) {
            $i = 0;
            foreach ($collection as $item) {
                $cate = $this->_objectManager->create('MGS\Portfolio\Model\Category')->getCollection()->addFieldToFilter('category_id', ['eq' => $item->getCategoryId()])->getFirstItem();
                $i++;
                if ($cate->getIdentifier() != '') {
                    $html .= '<a href="' . $this->getUrl('portfolio/' . $cate->getIdentifier()) . '">' . $item->getName() . '</a>';
                } else {
                    $html .= '<a href="' . $this->getUrl('portfolio/category/view', ['id' => $cate->getId()]) . '">' . $item->getName() . '</a>';
                }

                if ($i < count($collection)) {
                    $html .= ', ';
                }
            }
        }
        return $html;
    }

    public function getMenu()
    {
        $menu = $this->getModel()->getCollection();

        foreach ($menu as $cate) {
            if ($cate->getIdentifier() != '') {
                $cate->setLinkCate($this->getUrl('portfolio/' . $cate->getIdentifier()));
            } else {
                $cate->setLinkCate($this->getUrl('portfolio/category/view', ['id' => $cate->getId()]));
            }

        }
        return $menu;
    }

    public function getPagerHtml()
    {
        return $this->getLayout()->createBlock(
                'Magento\Theme\Block\Html\Pager',
                'mgs.portfolio.pager'
            )
            ->setTemplate('MGS_Portfolio::html/pager.phtml')
            ->setAvailableLimit(array(24 => 24, 48 => 48, 72 => 72))
            ->setShowPerPage(true)->setCollection(
                $this->getPortfolios()
            )
            ->toHtml();
    }
}
