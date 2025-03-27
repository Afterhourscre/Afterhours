<?php
/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * @category   BSS
 * @package    Bss_ChatGPT
 * @author     Extension Team
 * @copyright  Copyright (c) 2023-2024 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\ChatGPT\Model;

use Magento\Eav\Api\AttributeRepositoryInterface;
use Magento\Eav\Api\Data\AttributeInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\UrlInterface;

class ChatGPT
{
    public const CHAT_GPT_LIST_MESSAGE = [
        'product',
        'category',
        'cms',
        'seo_title',
        'seo_keyword',
        'seo_description'
    ];

    /**
     * @var array
     */
    protected $skipAttribute = [
        'short_description',
        'description',
        'category_ids'
    ];

    /**
     * @var \Bss\ChatGPT\Model\Config
     */
    protected $config;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $url;

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    protected $json;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var AttributeRepositoryInterface
     */
    protected $attributeRepository;

    /**
     * Construct.
     *
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param AttributeRepositoryInterface $attributeRepository
     * @param Config $config
     * @param UrlInterface $url
     * @param Json $json
     */
    public function __construct(
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Eav\Api\AttributeRepositoryInterface $attributeRepository,
        \Bss\ChatGPT\Model\Config $config,
        \Magento\Framework\UrlInterface $url,
        \Magento\Framework\Serialize\Serializer\Json $json
    ) {
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->attributeRepository = $attributeRepository;
        $this->config = $config;
        $this->url = $url;
        $this->json = $json;
    }

    /**
     * Get all mess default in general config
     *
     * @param int $storeId
     * @return bool|string
     */
    public function getAllMessDefault($storeId = null)
    {
        $result = [];

        foreach (self::CHAT_GPT_LIST_MESSAGE as $request) {
            $result[$request]['role'] = $this->config->getDefaultSystemRole($request, $storeId);
            $result[$request]['prompt'] = $this->config->getDefaultPrompt($request, $storeId);
        }

        return $this->json->serialize($result);
    }

    /**
     * Get url call API
     *
     * @param string $path
     * @return string
     */
    public function getUrlChatGPT($path = 'chatgpt/chatgpt/api')
    {
        return $this->url->getUrl($path);
    }

    /**
     * Get all attributes of product.
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return array
     */
    public function getAllAttributes($product)
    {
        $result = [];
        $allAttributes = $product->getAttributes();

        foreach ($allAttributes as $attribute) {
            if (in_array($attribute->getAttributeCode(), $this->skipAttribute)) {
                continue;
            }
            if ($label = $attribute->getStoreLabel()) {
                try {
                    $value = $this->getValueAttribute($product, $attribute);
                } catch (\Exception $e) {
                    continue;
                }

                if ($value) {
                    $result['product[' . $attribute->getAttributeCode() . ']']['label'] = strip_tags($label);
                    $result['product[' . $attribute->getAttributeCode() . ']']['value'] = strip_tags($value);
                }
            }
        }

        return $result;
    }

    /**
     * @param $product
     * @param $attribute
     * @return mixed|string
     */
    protected function getValueAttribute($product, $attribute)
    {
        if ($attribute->usesSource()) {
            $value = $product->getAttributeText($attribute->getAttributeCode());
        } else {
            $value = $product->getData($attribute->getAttributeCode());
        }

        if ($value instanceof \Magento\Framework\Phrase) {
            $value = $value->getText();
        }
        if (is_array($value)) {
            $value = implode(",", $value);
        }
        return $value;
    }

    /**
     * @return array
     */
    public function getAllAttributesWithoutProduct()
    {
        $result = [];
        foreach ($this->getAllItemsAttributes() as $attribute) {
            if ($attribute->getIsVisible() && !in_array($attribute->getAttributeCode(), $this->skipAttribute)){
                $result[$attribute->getAttributeCode()] = strip_tags($attribute->getStoreLabel());
            }
        }
        return $result;
    }

    /**
     * @return AttributeInterface[]
     */
    protected function getAllItemsAttributes()
    {
        $searchCriteria = $this->searchCriteriaBuilder->create();
        $attributeRepository = $this->attributeRepository->getList(
            'catalog_product',
            $searchCriteria
        );
        return $attributeRepository->getItems();
    }

    /**
     * @param $product
     * @param $attributes
     * @return string
     */
    public function getAttributesProduct($product, $attributes)
    {
        $result='';
        $allAttributes = $product->getAttributes();

        foreach ($allAttributes as $attribute) {
            if (
                in_array($attribute->getAttributeCode(), $this->skipAttribute)
                || (is_array($attributes) && !in_array($attribute->getAttributeCode(), $attributes))
            ) {
                continue;
            }
            if ($label = $attribute->getStoreLabel()) {
                try {
                    $value = $this->getValueAttribute($product, $attribute);
                } catch (\Exception $e) {
                    continue;
                }

                if ($value) {
                    $result .= $label . ":" . $value . ";";
                }
            }
        }
        return $result;
    }

    /**
     * Prepare att before send api chatgpt
     *
     * @param $product
     * @param $dataApi
     * @return void
     */
    public function prepareDataApiAttributes($product, &$dataApi)
    {
        if (is_array($dataApi) && isset($dataApi['attributes']) && count($dataApi['attributes']) > 0) {
            $dataAtt = [];
            $allAttributes = $product->getAttributes();
            $dataApiAttributes = $dataApi['attributes'];
            foreach ($allAttributes as $attribute) {
                if (!in_array($attribute->getAttributeCode(), $this->skipAttribute)
                    && in_array($attribute->getAttributeCode(), $dataApiAttributes)
                ) {
                    $dataAtt[] = $attribute->getAttributeCode();
                }
            }
            $dataApi['attributes'] = $dataAtt;
        }
    }

    /**
     * Check if use all attributes then set attributes to list api
     *
     * @return array
     */
    public function prepareAttributesApiWithUseAllAttributes()
    {
        $result = [];
        foreach ($this->getAllItemsAttributes() as $attribute) {
            if ($attribute->getIsVisible() && !in_array($attribute->getAttributeCode(), $this->skipAttribute)){
                $result[] = $attribute->getAttributeCode();
            }
        }
        return $result;
    }
}
