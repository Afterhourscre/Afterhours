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


namespace Mirasvit\SeoSitemap\Plugin;


use Mirasvit\SeoSitemap\Model\Config;
use Mirasvit\SeoSitemap\Model\Sitemap;
use Psr\Log\LoggerInterface;

class PingSitemapPlugin
{
    private $config;

    private $logger;

    public function __construct(Config $config, LoggerInterface $logger)
    {
        $this->config = $config;
        $this->logger = $logger;
    }

    public function afterGenerateXml(Sitemap $subject, Sitemap $result): Sitemap
    {
        if ($this->config->getIsPingSitemap((int)$result->getStoreId())) {
            $sitemapUrl = $result->getSitemapUrl($result->getSitemapPath(), $result->getSitemapFilename());

            $pingUrl = sprintf('http://www.google.com/webmasters/sitemaps/ping?sitemap=%s', $sitemapUrl);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $pingUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_TIMEOUT, 90);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);

            $content        = curl_exec($ch);
            $httpStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            if ($httpStatusCode != 200) {
                $this->logger->warning(
                    'Something wrong with submiting the sitemap ' . $sitemapUrl . ' to Google ping service.',
                    [
                        'pingUrl'      => $pingUrl, 
                        'responseCode' => $httpStatusCode, 
                        'response'     => $content
                    ]
                );
            } else {
                $this->logger->info('Sitemap ' . $sitemapUrl . ' was submited to Google ping service.');
            }
        }

        return $result;
    }
}
