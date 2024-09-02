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

use Magento\Catalog\Model\ProductRepository;
use Magento\Customer\Model\ResourceModel\CustomerRepository;
use Mirasvit\Core\Service\SerializeService;
use Mirasvit\Review\Api\Data\ReviewInterface;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Mirasvit\Review\Model\ConfigProvider;
use Mirasvit\Review\Repository\ReviewRepository;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollectionFactory;

class VerifyingService
{
    protected $orderCollectionFactory;

    protected $reviewRepository;

    protected $customerRepository;

    protected $ordersByCustomer;

    protected $configurableProduct;

    protected $productRepository;

    protected $configProvider;

    public function __construct(
        ProductRepository      $productRepository,
        Configurable           $configurableProduct,
        ReviewRepository       $reviewRepository,
        OrderCollectionFactory $orderCollectionFactory,
        CustomerRepository     $customerRepository,
        ConfigProvider         $configProvider
    ) {
        $this->productRepository      = $productRepository;
        $this->configurableProduct    = $configurableProduct;
        $this->reviewRepository       = $reviewRepository;
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->customerRepository     = $customerRepository;
        $this->configProvider         = $configProvider;
    }

    public function updateIsVerified(bool $force = false): void
    {
        $reviews = $this->reviewRepository->getCollection();

        if (!$force) {
            $reviews->addFieldToFilter('reindexed_at', ['eq' => '0000-00-00 00:00:00']);
        }

        /** @var ReviewInterface $review */
        foreach ($reviews->getItems() as $review) {

            if ($review->getCustomerId()) {

                $productId = $review->getEntityPkValue();
                $childIds  = $this->configurableProduct->getChildrenIds($productId);
                $childIds  = array_shift($childIds);

                $orders = $this->orderCollectionFactory->create()
                    ->addAttributeToFilter('customer_id', $review->getCustomerId())
                    ->addAttributeToFilter('status', ['in' => $this->configProvider->getOrderStatusesForVerifiedBuyer()])
                    ->addAttributeToFilter('main_table.updated_at', ['lt' => $review->getCreatedAt()])
                    ->setOrder('main_table.updated_at', 'desc');

                $orders->getSelect()->join(
                    ['order_item' => $orders->getTable('sales_order_item')],
                    'main_table.entity_id = order_item.order_id'
                );

                if (!empty($childIds)) {
                    $orders->addFieldToFilter('order_item.product_id', ['in' => $childIds]);
                } else {
                    $orders->addFieldToFilter('order_item.product_id', ['eq' => $productId]);
                }

                if (count($orders) > 0) {
                    $order = $orders->getFirstItem();

                    if ($productId != $order->getProductId()) {
                        $productAttributes    = [];
                        $productAttributeData = [];
                        $parentProduct        = $this->productRepository->getById($productId);
                        $attributes           = $this->configurableProduct->getConfigurableAttributes($parentProduct);

                        foreach ($attributes as $attribute) {
                            $productAttributes[$attribute->getProductAttribute()->getAttributeCode()] = $attribute->getLabel();
                        }

                        $child = $this->productRepository->getById($order->getProductId());

                        ksort($productAttributes);

                        foreach ($productAttributes as $attributeCode => $attributeLabel) {
                            $attributeValue = $child->getResource()
                                ->getAttribute($attributeCode)
                                ->getFrontend()
                                ->getValue($child);

                            $productAttributeData[$attributeCode] = [
                                'label' => $attributeLabel,
                                'value' => $attributeValue,
                            ];
                        }

                        $review->setProductInfo($productAttributeData);
                    }

                    $review->setIsVerifiedBuyer(true);
                } else {
                    $review->setIsVerifiedBuyer(false);
                }
            }

            $review->setReindexedAt(date_create()->format('Y-m-d H:i:s'));
            $this->reviewRepository->save($review);
        }
    }
}
