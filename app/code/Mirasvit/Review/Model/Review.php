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


namespace Mirasvit\Review\Model;


use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Customer\Model\Customer;
use Magento\Customer\Model\CustomerFactory;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\Review\Model\Review\SummaryFactory;
use Mirasvit\Core\Service\SerializeService;
use Mirasvit\Review\Api\Data\ReviewInterface;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Review extends AbstractModel implements IdentityInterface, ReviewInterface
{
    /**
     * Cache tag
     */
    const CACHE_TAG = 'review_block';

    private $productRepository;

    private $customerFactory;

    private $summaryFactory;

    /**
     * Event prefix for observer
     * @var string
     */
    protected $_eventPrefix = 'review';

    public function __construct(
        SummaryFactory             $summaryFactory,
        ProductRepositoryInterface $productRepository,
        CustomerFactory            $customerFactory,
        Context                    $context,
        Registry                   $registry,
        AbstractResource           $resource = null,
        AbstractDb                 $resourceCollection = null,
        array                      $data = []
    ) {
        $this->productRepository = $productRepository;
        $this->customerFactory   = $customerFactory;
        $this->summaryFactory    = $summaryFactory;

        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    protected function _construct()
    {
        $this->_init(\Mirasvit\Review\Model\ResourceModel\Review::class);
    }

    public function getTitle(): string
    {
        return (string)$this->getData(self::TITLE);
    }

    public function setTitle(string $value): ReviewInterface
    {
        return $this->setData(self::TITLE, $value);
    }

    public function getDetail(): string
    {
        return (string)$this->getData(self::DETAIL);
    }

    public function setDetail(string $value): ReviewInterface
    {
        return $this->setData(self::DETAIL, $value);
    }

    public function getProductInfo(): ?array
    {
        $productInfoJson = $this->getData(self::PRODUCT_INFO);

        if (!$productInfoJson) {
            return null;
        }

        $productInfo = SerializeService::decode($productInfoJson);

        return $productInfo;
    }

    public function setProductInfo(array $value): self
    {
        $data = null;


        if (!empty($value)) {
            $data = SerializeService::encode($value);
        }

        return $this->setData(self::PRODUCT_INFO, $data);
    }

    public function getProductId(): int
    {
        return (int)$this->getData(self::PRODUCT_ID);
    }

    public function setProductId(int $value): ReviewInterface
    {
        return $this->setData(self::PRODUCT_ID, $value);
    }

    public function getCustomerId(): ?int
    {
        $customerId = $this->getData(self::CUSTOMER_ID);

        return $customerId ? (int)$customerId : null;
    }

    public function setCustomerId(?int $value): ReviewInterface
    {
        return $this->setData(self::CUSTOMER_ID, $value);
    }

    public function getNickname(): string
    {
        return (string)$this->getData(self::NICKNAME);
    }

    public function setNickname(string $value): ReviewInterface
    {
        return $this->setData(self::NICKNAME, $value);
    }


    public function getStoreId(): int
    {
        return (int)$this->getData(self::STORE_ID);
    }

    public function setStoreId(int $value): ReviewInterface
    {
        return $this->setData(self::STORE_ID, $value);
    }

    public function getCreatedAt(): string
    {
        return $this->getData(self::CREATED_AT);
    }

    public function setCreatedAt(string $value): ReviewInterface
    {
        return $this->setData(self::CREATED_AT, $value);
    }

    public function getPros(): string
    {
        return (string)$this->getData(self::PROS);
    }

    public function setPros(string $value): ReviewInterface
    {
        return $this->setData(self::PROS, $value);
    }

    public function getCons(): string
    {
        return (string)$this->getData(self::CONS);
    }

    public function setCons(string $value): ReviewInterface
    {
        return $this->setData(self::CONS, $value);
    }

    public function getIsVerifiedBuyer(): bool
    {
        return (bool)$this->getData(self::IS_VERIFIED_BUYER);
    }

    public function setIsVerifiedBuyer(bool $value): ReviewInterface
    {
        return $this->setData(self::IS_VERIFIED_BUYER, $value);
    }

    public function getReindexedAt(): string
    {
        return (string)$this->getData(self::REINDEXED_AT);
    }

    public function setReindexedAt(string $value): ReviewInterface
    {
        return $this->setData(self::REINDEXED_AT, $value);
    }

    public function getIp(): string
    {
        return (string)$this->getData(self::IP);
    }

    public function setIp(string $value): self
    {
        return $this->setData(self::IP, $value);
    }

    public function getCountry(): string
    {
        return (string)$this->getData(self::COUNTRY);
    }

    public function setCountry(string $value): self
    {
        return $this->setData(self::COUNTRY, $value);
    }

    public function getCountryIso(): string
    {
        return (string)$this->getData(self::COUNTRY_ISO);
    }

    public function setCountryIso(string $value): self
    {
        return $this->setData(self::COUNTRY_ISO, $value);
    }

    public function getLocation(): string
    {
        return (string)$this->getData(self::LOCATION);
    }

    public function setLocation(string $value): self
    {
        return $this->setData(self::LOCATION, $value);
    }

    public function getProductIds(): array
    {
        $arrayIds   = [];
        $productIds = $this->getData(self::PRODUCT_IDS);
        if ($productIds) {
            $arrayIds = explode(',', $productIds);
        }

        return $arrayIds;
    }

    public function setProductIds(array $value): self
    {
        $productIds = implode(',', $value);

        return (string)$this->setData(self::PRODUCT_IDS, $productIds);

    }

    public function getIdentities()
    {
        $tags = [];
        if ($this->getProductId()) {
            $tags[] = Product::CACHE_TAG . '_' . $this->getProductId();
        }

        return $tags;
    }

    public function getProduct(): ?ProductInterface
    {
        if (!$this->getProductId()) {
            return null;
        }

        try {
            return $this->productRepository->getById($this->getProductId());
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            return null;
        }
    }

    public function getCustomer(): ?Customer
    {
        if (!$this->getCustomerId()) {
            return null;
        }

        try {
            return $this->customerFactory->create()->load($this->getCustomerId());
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            return null;
        }
    }

    public function getStores(): array
    {
        $stores = $this->getData('stores');

        if (!$stores) {
            return [0];
        }

        return is_array($stores) ? $stores : explode(',', (string)$stores);
    }

    public function getEntityIdByCode(string $entityCode): ?string
    {
        return $this->getResource()->getEntityIdByCode($entityCode) ? : null;
    }

    public function aggregate(): ReviewInterface
    {
        $this->getResource()->aggregate($this);

        return $this;
    }

    public function getEntitySummary($product, $storeId = 0)
    {
        $summaryData = $this->summaryFactory->create()->setStoreId($storeId)->load($product->getId());
        $summary     = new \Magento\Framework\DataObject();
        $summary->setData($summaryData->getData());
        $product->setRatingSummary($summary);
    }

    public function validate()
    {
        $errors = [];

        if (!trim($this->getTitle())) {
            $errors[] = __('Please enter a review summary.');
        }

        if (!trim($this->getNickname())) {
            $errors[] = __('Please enter a nickname.');
        }

        if (!trim($this->getDetail())) {
            $errors[] = __('Please enter a review.');
        }

        if (empty($errors)) {
            return true;
        }

        return $errors;
    }

    public function loadRatingVotes(): ReviewInterface
    {
        if (!$this->getRatingVotes()) {
            $this->getResource()->loadRatingVotes($this);
        }

        return $this;
    }
}
