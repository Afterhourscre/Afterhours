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


namespace Mirasvit\SeoAi\Service;


use Mirasvit\Core\Service\SerializeService;
use Mirasvit\SeoAudit\Api\Data\UrlInterface;

class PromptService
{
    const QUERY_PARAM = 'mstseoai';

    private $contextPool = [];

    public function preparePrompt(UrlInterface $url, string $type, string $language): ?string
    {
        $context = $this->getContext($url);

        if (!$context || !count($context)) {
            return null;
        }

        $field = $this->fieldToLabel($type);

        $prompt = "Write a new {$field} for the website page in " . $language . " language using following information:" . PHP_EOL;
        $prompt .= $this->toString($context) . PHP_EOL;
        $prompt .= "LIMIT: {$field} MUST be between " . ($type == 'meta_title' ? '50 and 70' : '110 and 160') . " characters." . PHP_EOL;
        $prompt .= $field . ": ";

        return $prompt;
    }

    private function getContext(UrlInterface $url): ?array
    {
        if (!isset($this->contextPool[$url->getUrl()])) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->prepareRequestUrl($url->getUrl()));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_TIMEOUT, 90);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);

            $content        = curl_exec($ch);
            $httpStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            curl_close($ch);

            if ($httpStatusCode != 200 || !$content) {
                return null;
            }

            $context = SerializeService::decode($content);

            if (!is_array($context)) {
                return null;
            }

            $this->contextPool[$url->getUrl()] = $context;
        }

        return $this->contextPool[$url->getUrl()];
    }

    private function prepareRequestUrl(string $url): string
    {
        $additionalParam = '?' . self::QUERY_PARAM . '=' . time();

        return strpos($url, '?') !== false
            ? str_replace('?', $additionalParam, $url)
            : $url . $additionalParam;
    }

    private function toString(array $context): string
    {
        $output = '';

        foreach ($context as $key => $data) {
            $output .= $this->fieldToLabel($key) . ' Data: ' . PHP_EOL;

            foreach ($data as $datum) {
                $output .= ' - ' . $datum['label'] . ': '
                    . (is_array($datum['value']) ? implode(', ', $datum['value']) : $datum['value'])
                    . PHP_EOL;
            }
        }

        return $output;
    }

    public function fieldToLabel(string $field): string
    {
        return ucwords(str_replace('_', ' ', $field));
    }
}
