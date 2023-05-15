<?php
/**
 * Created by PhpStorm.
 * User: michael
 * Date: 12.03.19
 * Time: 12:38
 */

namespace MageCloud\NoIndexNoFollow\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\RequestInterface;

/**
 * Class NoindexNofollowPagesWithDynamicVariable
 * @package MageCloud\NoIndexNoFollow\Observer
 */

class NoindexNofollowPagesWithDynamicVariable implements ObserverInterface
{
    const ROBOTS_STRATEGY_NOINDEX_NOFOLLOW = 'NOINDEX,NOFOLLOW';
    const ROBOTS_STRATEGY_NOINDEX_FOLLOW = 'NOINDEX,FOLLOW';

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $requestHttp;

    /**
     * @var \Magento\Framework\View\Page\Config
     */
    protected $layoutFactory;

    /**
     * NoindexNofollowPagesWithDynamicVariable constructor.
     * @param RequestInterface $request
     * @param \Magento\Framework\App\Request\Http $requestHttp
     * @param \Magento\Framework\View\Page\Config $layoutFactory
     */
    public function __construct(
        RequestInterface $request,
        \Magento\Framework\App\Request\Http $requestHttp,
        \Magento\Framework\View\Page\Config $layoutFactory
    ) {
        $this->request = $request;
        $this->requestHttp = $requestHttp;
        $this->layoutFactory = $layoutFactory;
    }

    /**
     * @param Observer $observer
     * @return $this
     */
    public function execute(Observer $observer)
    {
//        $fullActionName = $observer->getEvent()->getFullActionName();

        /**
         * @var \Magento\Framework\View\LayoutInterface $layout
         */
//        $layout = $observer->getEvent()->getLayout();

        $query = $this->requestHttp->getQueryValue();
        $pathInfo = $this->requestHttp->getOriginalPathInfo();
        if (!empty($query) || preg_match('/^\/catalogsearch/', $pathInfo) || preg_match('/^\/catalog\/category/', $pathInfo) || preg_match('/^\/catalog\/product/', $pathInfo)
        ) {
        // set noindex, nofollow for pages with dynamic variable(s)
        $this->layoutFactory->setRobots(self::ROBOTS_STRATEGY_NOINDEX_NOFOLLOW);

            // if query has parameter 'p' and it's only one parameter, then robots must be set to noindex, follow
            if (array_key_exists('p', $query) && (count($query) == 1)) {
                $this->layoutFactory->setRobots(self::ROBOTS_STRATEGY_NOINDEX_FOLLOW);
            }
        }

        return $this;
    }
}