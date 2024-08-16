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



namespace Mirasvit\Review\Service;

use GeoIp2\Database\Reader as GeoIp2Reader;
use  Mirasvit\Review\Api\Data\ReviewInterface;
use Mirasvit\Review\Repository\ReviewRepository;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollectionFactory;

class LocationSevice
{
    private $reviewRepository;

    private $orderCollectionFactory;

    private $remoteAddress;

    public function __construct(
        RemoteAddress $remoteAddress,
        ReviewRepository $reviewRepository,
        OrderCollectionFactory $orderCollectionFactory
    ) {
        $this->remoteAddress          = $remoteAddress;
        $this->reviewRepository       = $reviewRepository;
        $this->orderCollectionFactory = $orderCollectionFactory;
    }

    /**
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        $reader = new GeoIp2Reader(dirname(dirname(__FILE__)) . '/Setup/GeoLite2-City.mmdb');

        $collection = $this->reviewRepository->getCollection();

        $productIdsByCustomer = [];

        /** @var ReviewInterface $review */
        foreach ($collection as $review) {
            if ($review->getCountry()) {
                continue;
            }

            $storeId = $review->getStoreId();

            if ($review->getCustomerId()) {
                if (!isset($productIdsByCustomer[$review->getCustomerId()])) {
                    $orders = $this->orderCollectionFactory->create();
                    $orders->addFieldToFilter('customer_id', $review->getCustomerId());

                    $productIds = [];
                    /** @var \Magento\Sales\Model\Order $order */
                    foreach ($orders as $order) {
                        foreach ($order->getAllVisibleItems() as $item) {
                            $productIds[] = $item->getProductId();
                        }
                    }
                    $productIds = array_filter(array_unique($productIds));

                    $productIdsByCustomer[$review->getCustomerId()] = $productIds;
                }
                $order   = $orders->getFirstItem();
                $storeId = $order->getStoreId();
            }

            $ip = $review->getIp();

            if (!$ip) {
                continue;
            }

            $data = false;
            try {
                $data = $reader->city($ip);
            } catch (\Exception $e) {
            }

            if ($data) {
                $toUpdate[ReviewInterface::COUNTRY_ISO] = $data->country->isoCode;
                $toUpdate[ReviewInterface::COUNTRY]     = $data->country->name;
                $toUpdate[ReviewInterface::LOCATION]    = $data->city->name;
            }

            $stores = $review->getStores();

            if (!in_array($storeId, $stores)) {
                $stores[] = (int)$storeId;
            }

            $toUpdate[ReviewInterface::STORES]      = $stores;
            $toUpdate[ReviewInterface::PRODUCT_IDS] = $review->getCustomerId() && !empty($productIdsByCustomer[$review->getCustomerId()])
                ? implode(',', $productIdsByCustomer[$review->getCustomerId()])
                : null;

            foreach ($toUpdate as $key => $value) {
                $review->setData($key, $value);
            }
            $this->reviewRepository->save($review);
        }
    }
}
