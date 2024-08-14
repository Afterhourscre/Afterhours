<?php

declare(strict_types=1);

namespace MageCloud\RemoveDefaultRichSnippets\Plugin;

use Closure;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Response\Http;
use Magento\Framework\Controller\ResultInterface;

class RemoveRichSnippetsPlugin
{
    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * RemoveRichSnippetsPlugin constructor.
     *
     * @param RequestInterface $request
     */
    public function __construct(
        RequestInterface $request
    ) {
        $this->request = $request;
    }

    /**
     * @param ResultInterface $subject
     * @param Closure $proceed
     * @param Http $response
     *
     * @return mixed
     */
    public function aroundRenderResult(
        ResultInterface $subject,
        Closure $proceed,
        Http $response
    ) {
        $result = $proceed($response);

        if (
            $this->request->getFullActionName() !== 'catalog_product_view'
            || PHP_SAPI === 'cli'
            || $this->request->isXmlHttpRequest()
        ) {
            return $result;
        }

        $body = $response->getBody();
        $body = $this->clearNativeSnippets($body);

        $response->setBody($body);

        return $result;
    }

    /**
     * Remove itemprop, itemscope from breadcrumbs html
     *
     * @param string $html
     *
     * @return array|string|null
     */
    public function clearNativeSnippets($html)
    {
        $pattern = [
            '/itemscope=""/i',
            '/itemscope\s/i',
            '/itemprop="(.*?)"/i',
            '/itemprop=\'(.*?)\'/i',
            '/itemtype="(.*?)"/i',
            '/itemtype=\'(.*?)\'/i',
            '/itemscope="(.*?)"/i',
            '/itemscope=\'(.*?)\'/i',
            '/itemscope=\'\'/i',
        ];

        return preg_replace($pattern, '', $html);
    }
}