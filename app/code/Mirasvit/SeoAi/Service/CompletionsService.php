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


use Mirasvit\SeoAi\Model\ConfigProvider;

class CompletionsService extends AbstractApi
{
    private $configProvider;

    public function __construct(ConfigProvider $configProvider)
    {
        $this->configProvider = $configProvider;
    }

    public function answer(string $text): ?string
    {
        if (!$this->configProvider->isHelperEnabled()) {
            return null;
        }

        $key = $this->configProvider->getApiKey();

        if (!$key) {
            throw new \RuntimeException('OpenAI key is not set.');
        }

        $input = [
            //https://platform.openai.com/docs/models/gpt-3-5
            'model'    => $this->configProvider->getModel(),
            'messages' => [
                ["role" => "user", "content"=> $text],
            ],
        ];

        $result = $this->request($key, 'chat/completions', $input);
        foreach ($result['choices'] as $choice) {
            $message = $choice['message'];

            $answer = $message['content'];
            $answer = trim($answer);
            $answer = trim($answer, "\n");
            $answer = trim($answer, "\r");
            $answer = trim($answer, "\"");

            return $answer;
        }

        return '';
    }
}
