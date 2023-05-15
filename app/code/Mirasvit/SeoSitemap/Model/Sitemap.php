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



namespace Mirasvit\SeoSitemap\Model;

use Mirasvit\SeoSitemap\Helper\Data as HelperData;
use Mirasvit\SeoSitemap\Helper\Markup as HelperMarkup;
use Mirasvit\SeoSitemap\Model\Config\LinkSitemapConfig;
use Mirasvit\SeoSitemap\Repository\ProviderRepository;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\ConsoleOutput;

class Sitemap extends \Magento\Sitemap\Model\Sitemap
{
    private $providerRepository;

    private $linkSitemapConfig;

    private $seoSitemapData;

    private $helperMarkup;

    private $moduleManager;

    private $objectManager;

    public function __construct(
        LinkSitemapConfig $linkSitemapConfig,
        HelperData $seoSitemapData,
        HelperMarkup $helperMarkup,
        ProviderRepository $providerRepository,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Escaper $escaper,
        \Magento\Sitemap\Helper\Data $sitemapData,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Sitemap\Model\ResourceModel\Catalog\CategoryFactory $categoryFactory,
        \Magento\Sitemap\Model\ResourceModel\Catalog\ProductFactory $productFactory,
        \Magento\Sitemap\Model\ResourceModel\Cms\PageFactory $cmsFactory,
        \Magento\Framework\Stdlib\DateTime\DateTime $modelDate,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\Stdlib\DateTime $dateTime,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->linkSitemapConfig  = $linkSitemapConfig;
        $this->seoSitemapData     = $seoSitemapData;
        $this->helperMarkup       = $helperMarkup;
        $this->providerRepository = $providerRepository;
        $this->moduleManager      = $moduleManager;
        $this->objectManager      = $objectManager;

        parent::__construct(
            $context,
            $registry,
            $escaper,
            $sitemapData,
            $filesystem,
            $categoryFactory,
            $productFactory,
            $cmsFactory,
            $modelDate,
            $storeManager,
            $request,
            $dateTime,
            $resource,
            $resourceCollection,
            $data
        );
    }

    /**
     * @return void
     */
    protected function _initSitemapItems()
    {
        $storeId = $this->getStoreId();
        foreach ($this->providerRepository->initSitemapItems($storeId) as $items) {
            if (is_array($items)) {
                foreach ($items as $item) {
                    $this->_sitemapItems[] = $item;
                }
            } elseif ($items) {
                $this->_sitemapItems[] = $items;
            }
        }

        $this->_tags = $this->helperMarkup->getTagsData();
    }

    /**
     * @return $this
     */
    public function generateXml()
    {
        $this->_initSitemapItems();
        $excludedLinks     = $this->linkSitemapConfig->getExcludeLinks();
        $redirectHelper    = $this->getRedirectHelper();
        $progressBarSingle = false;

        if (php_sapi_name() == "cli") {
            $output            = new ConsoleOutput();
            $progressBarSingle = new ProgressBar($output);
            $progressBarSingle->setProgressCharacter('#');
            $progressBarSingle->setRedrawFrequency(100);
            $progressBarSingle->setFormat('%current% %bar% %memory:2s%');

            $progressBarSingle->start();
        }

        foreach ($this->_sitemapItems as $sitemapItem) {
            $changeFreq = $sitemapItem->getChangefreq();
            $priority   = $sitemapItem->getPriority();

            foreach ($sitemapItem->getCollection() as $item) {
                if ($this->seoSitemapData->checkArrayPattern($item->getUrl(), $excludedLinks)) {
                    continue;
                }

                if ($redirectHelper) {
                    $url = $redirectHelper->getUrlWithCorrectEndSlash($item->getUrl());
                    $item->setUrl($url);
                }

                $xml = $this->_getSitemapRow($item->getUrl(), $item->getUpdatedAt(), $changeFreq, $priority, $item->getImages());
                if ($this->_isSplitRequired($xml) && $this->_sitemapIncrement > 0) {
                    $this->_finalizeSitemap();
                }

                if (!$this->_fileSize) {
                    $this->_createSitemap();
                }
                $this->_writeSitemapRow($xml);
                $this->_lineCount++;
                $this->_fileSize += strlen($xml);
                if ($progressBarSingle) {
                    $progressBarSingle->advance();
                }
            }
        }

        if ($progressBarSingle) {
            $progressBarSingle->finish();
            $output->write("\033[1A");
        }
        $this->_finalizeSitemap();

        if ($this->_sitemapIncrement == 1) {
            $path        = rtrim($this->getSitemapPath(), '/') . '/' . $this->_getCurrentSitemapFilename($this->_sitemapIncrement);
            $destination = rtrim($this->getSitemapPath(), '/') . '/' . $this->getSitemapFilename();
            $this->_directory->renameFile($path, $destination);
        } else {
            $this->_createSitemapIndex();
        }

        if ($this->_isEnabledSubmissionRobots()) {
            $this->_addSitemapToRobotsTxt($this->getSitemapFilename());
        }

        $this->setSitemapTime($this->_dateModel->gmtDate('Y-m-d H:i:s'));
        $this->save();

        return $this;
    }

    /**
     * @return string
     */
    protected function _getSitemapRow($url, $lastmod = null, $changefreq = null, $priority = null, $images = null)
    {
        $row = '<loc>' . htmlspecialchars($this->_getUrl($url)) . '</loc>';
        if ($lastmod) {
            $row .= '<lastmod>' . $this->_getFormattedLastmodDate($lastmod) . '</lastmod>';
        }
        if ($changefreq) {
            $row .= '<changefreq>' . $changefreq . '</changefreq>';
        }
        if ($priority) {
            $row .= sprintf('<priority>%.1f</priority>', $priority);
        }
        if ($images) {
            $row .= $this->getRowImageMarkup($images);
        }

        return '<url>' . $row . '</url>';
    }

    /**
     * @return string
     */
    private function getRowImageMarkup($images)
    {
        $imagesMarkup = [];
        foreach ($images->getCollection() as $image) {
            $imagesMarkup[] = $this->helperMarkup->getImageMarkup(
                htmlspecialchars($this->_getMediaUrl($image->getUrl())),
                htmlspecialchars($images->getTitle()),
                htmlspecialchars($image->getCaption())
            );
        }

        $imagesMarkup[] = $this->helperMarkup->afterGetImageMarkup(
            htmlspecialchars($images->getTitle()),
            htmlspecialchars($this->_getMediaUrl($images->getThumbnail())),
            htmlspecialchars($images->getAlt())
        );

        return implode('', $imagesMarkup);
    }

    /**
     * @return string
     */
    protected function _getMediaUrl($url)
    {
        if (strpos($url, 'http://') !== false
            || strpos($url, 'https://') !== false) {
            $baseUrl = $this->_storeManager->getStore($this->getStoreId())
                ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
            if (strpos($url, $baseUrl) === false) {
                $url = preg_replace('/(.*?)\\/media\\//ims', $baseUrl . '/', $url, 1);
            }
            $urlExploded    = explode('://', $url);
            $urlExploded[1] = str_replace('//', '/', $urlExploded[1]);
            $url            = implode('://', $urlExploded);
            if (strpos($baseUrl, '/pub/') === false) {
                $url = str_replace('/pub/', '/', $url);
            }

            return $url;
        }

        return $this->_getUrl($url, \Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
    }

    /**
     * @return \Mirasvit\Seo\Helper\Redirect|bool
     */
    public function getRedirectHelper()
    {
        return $this->moduleManager->isEnabled('Mirasvit_Seo') ?
            $this->objectManager->get('\Mirasvit\Seo\Helper\Redirect') : false;
    }

    /**
     * @return string
     */
    protected function _getSitemapIndexRow($sitemapFilename, $lastmod = null)
    {
        $url = $this->getSitemapUrl($this->getSitemapPath(), $sitemapFilename);
        $row = '<loc>' . htmlspecialchars($url) . '</loc>';
        if ($lastmod) {
            $row .= '<lastmod>' . $this->_getFormattedLastmodDate($lastmod) . '</lastmod>';
        }

        return '<sitemap>' . $row . '</sitemap>';
    }
}
