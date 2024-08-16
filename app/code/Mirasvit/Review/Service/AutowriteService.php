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
 * @package   mirasvit/module-review
 * @version   1.1.2
 * @copyright Copyright (C) 2024 Mirasvit (https://mirasvit.com/)
 */


declare(strict_types=1);


namespace Mirasvit\Review\Service;


use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\ProductRepository;
use Mirasvit\Core\Service\SerializeService;
use Mirasvit\Review\Model\ConfigProvider;
use Mirasvit\Review\Service\Context\ProductContext;

class AutowriteService
{
    private $completionService;

    private $productRepository;

    private $productContext;

    private $configProvider;

    public function __construct(
        CompletionService $completionService,
        ProductRepository $productRepository,
        ProductContext $productContext,
        ConfigProvider $configProvider
    ) {
        $this->completionService = $completionService;
        $this->productRepository = $productRepository;
        $this->productContext    = $productContext;
        $this->configProvider    = $configProvider;
    }

    public function generateReview(int $productId, string $additional, int $storeId = null): array
    {
        $prompt   = $this->preparePrompt($productId, $additional, $storeId);
        $response = $this->completionService->answer($prompt);

        $result = [
            'review'       => $this->processResponse($response),
            'raw_response' => $response,
            'prompt'       => $prompt
        ];

        return $result;
    }

    protected function preparePrompt(int $productId, string $additionalInstructions = null, int $storeId = null): string
    {
        $product     = $this->productRepository->getById($productId, false, $storeId);
        $productData = $this->productContext->contextByEntity($product);

        if (!count($productData)) {
            return '';
        }

        $language = 'the same language as the original text';

        if ($storeId) {
            $language = $this->configProvider->getLanguageByStore($storeId);
        }

        $productDetails = $productData[count($productData)-1]['value'];

        $prompt = <<<text
        You are a customer who bought the product.
        Here is the information about the product you have bought:
        $productDetails
        Write a Review about this product.
        Return a JSON with the following fields:
        'title' - short summary of a review,
        'detail' - review details
        'pros' - bullet list of positive points about the product,
        'cons' - bullet list of negative points about the product
        Consider the following information when writing the review:
        $additionalInstructions
        Write in $language.
        Review:
        text;

        return $prompt;
    }

    protected function processResponse(string $response): array
    {
        $result = SerializeService::decode($response);

        if (!is_array($result)) {
            return [];
        }

        return is_array($result) ? $result : [];
    }
}
