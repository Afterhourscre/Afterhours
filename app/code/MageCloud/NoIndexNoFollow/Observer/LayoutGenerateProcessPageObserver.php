<?php
/**
 * @author andy
 * @email andyworkbase@gmail.com
 * @team MageCloud
 */
namespace MageCloud\NoIndexNoFollow\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Request\Http as Request;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Asset\GroupedCollection;

/**
 * Class LayoutGenerateProcessPageObserver
 * @package MageCloud\NoIndexNoFollow\Observer
 */
class LayoutGenerateProcessPageObserver implements ObserverInterface
{
    /**
     * Robots strategy
     */
    const ROBOTS_NOINDEX_NOFOLLOW_STRATEGY = 'NOINDEX,NOFOLLOW';
    const ROBOTS_NOINDEX_FOLLOW_STRATEGY = 'NOINDEX,FOLLOW';
    const ROBOTS_INDEX_FOLLOW_STRATEGY = 'INDEX,FOLLOW';

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var \Magento\Framework\View\Page\Config
     */
    protected $pageConfig;

    /**
     * LayoutLoadBeforeProcessPageObserver constructor.
     * @param RequestInterface $request
     * @param UrlInterface $urlBuilder
     * @param \Magento\Framework\View\Page\Config $pageConfig
     */
    public function __construct(
        RequestInterface $request,
        UrlInterface $urlBuilder,
        \Magento\Framework\View\Page\Config $pageConfig
    ) {
        $this->request = $request;
        $this->urlBuilder = $urlBuilder;
        $this->pageConfig = $pageConfig;
    }

    /**
     * @param Observer $observer
     * @return $this
     */
    public function execute(Observer $observer)
    {
        $query = $this->request->getQueryValue();
        $pathInfo = $this->request->getOriginalPathInfo();
        // set noindex, nofollow strategy for specific pages:
        // - with dynamic variable(s)
        // - customer pages
        if (!empty($query) || preg_match('/^\/customer/', $pathInfo)) {
            $this->pageConfig->setRobots(self::ROBOTS_NOINDEX_NOFOLLOW_STRATEGY);
            // if query has parameter 'p' and it's only one parameter, then robots must be set to noindex, follow
            if (array_key_exists('p', $query) && (count($query) == 1)) {
                $this->pageConfig->setRobots(self::ROBOTS_NOINDEX_FOLLOW_STRATEGY);
            } else if (array_key_exists('amp', $query)) {
                // index, follow for AMP pages
                $this->pageConfig->setRobots(self::ROBOTS_INDEX_FOLLOW_STRATEGY);
            }
        }

        // try to set correct canonical url for urls with query parameters
        if (!empty($query)) {
            $currentUrl = $this->getCurrentUrl();
            $canonicalUrl = $this->getCleanCanonicalUrl($currentUrl);

            /** @var \Magento\Framework\View\Asset\GroupedCollection $assetCollection */
            $assetCollection = $this->getAssetCollection();
            if (!$assetCollection) {
                return $this;
            }

            $canonicalGroup = $assetCollection->getGroupByContentType('canonical');
            if (!$canonicalGroup) {
                $this->addCanonicalUrl($canonicalUrl);
            } else if ($canonicalGroup->has($currentUrl)) {
                // remove old canonical url
                $canonicalGroup->remove($currentUrl);
                $this->addCanonicalUrl($canonicalUrl);
            }
        }

        return $this;
    }

    /**
     * @param $canonicalUrl
     * @return $this
     */
    private function addCanonicalUrl($canonicalUrl)
    {
        $this->pageConfig->addRemotePageAsset(
            $canonicalUrl,
            'canonical',
            ['attributes' => ['rel' => 'canonical']]
        );

        return $this;
    }

    /**
     * @return GroupedCollection
     */
    private function getAssetCollection()
    {
        return $this->pageConfig->getAssetCollection();
    }

    /**
     * @return string
     */
    private function getCurrentUrl()
    {
        return $this->urlBuilder->getCurrentUrl();
    }

    /**
     * @param $url
     * @return mixed
     */
    private function getCleanCanonicalUrl($url)
    {
        return preg_replace('/\?.*/', '', $url);
    }
}