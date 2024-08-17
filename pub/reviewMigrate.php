<?php

use \Magento\Framework\App\Bootstrap;
use \Magento\Framework\App\Area;
use Mirasvit\Review\Api\Data\ReviewInterface;

include('../app/bootstrap.php');

$bootstrap = Bootstrap::create(BP, $_SERVER);
$objectManager = $bootstrap->getObjectManager();

// Set area code to frontend
$state = $objectManager->get('Magento\Framework\App\State');
$state->setAreaCode('frontend');

// Fetch Review Data from `aw_ar_review` Table
try {
    $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
    $connection = $resource->getConnection();
    $sql = "SELECT * FROM aw_ar_review";
    $result = $connection->fetchAll($sql);
    
    $reviewRepository = $objectManager->get('Mirasvit\Review\Repository\ReviewRepository');
    $storeManager = $objectManager->get('Magento\Store\Model\StoreManagerInterface');
    $ratingFactory = $objectManager->get('Magento\Review\Model\RatingFactory');
    $logger = $objectManager->get('Psr\Log\LoggerInterface');

    foreach ($result as $row) {
    	//if($row['customer_id'] == 338){
        try {
            // Prepare review data
            $data = [
                'created_at' => $row['created_at'],
                'title' => $row['summary'],
                'detail' => $row['content'],
                'nickname' => $row['nickname'],
                'store_id' => $row['store_id'],
                'product_id' => $row['product_id'],
                'status' => $row['status'],
                'customer_id' => $row['customer_id'], // Set customer ID if available
                'email' => $row['email'],
                'is_verified_buyer' => $row['is_verified_buyer'],
                'votes_positive' => $row['votes_positive'],
                'votes_negative' => $row['votes_negative'],
                'product_recommended' => $row['product_recommended'],
                'is_featured' => $row['is_featured'],
            ];

            // Create a new review and set data
            $review = $reviewRepository->create()->setData($data);
            $review->unsetData('review_id'); // Unset old ID to generate a new one

            // Ensure customer_id is set if available
            if (!empty($row['customer_id'])) {
                $review->setCustomerId((int)$row['customer_id']);
            }

            $review->setEntityId($review->getEntityIdByCode('product'))
                ->setProductId($row['product_id'])
                ->setStatusId($row['status']) // Adjust this if needed
                ->setStoreId($row['store_id'])
                ->setStores([$row['store_id']]);

            // Save the review
            $reviewRepository->save($review);


				$ratingValue = 0;
				if ($row['rating'] >= 80) {
				    $ratingValue = 5;
				} elseif ($row['rating'] >= 60) {
				    $ratingValue = 4;
				} elseif ($row['rating'] >= 40) {
				    $ratingValue = 3;
				} elseif ($row['rating'] >= 20) {
				    $ratingValue = 2;
				} else {
				    $ratingValue = 1;
				}
            // Check if rating is valid before saving
            if (!empty($row['rating']) && is_numeric($row['rating'])) {
               $ratingFactory->create()
				    ->setRatingId(1)
				    ->setReviewId($review->getId())
				    ->setCustomerId($row['customer_id'] ?? null)
				    ->addOptionVote($ratingValue, $row['product_id']);
            } else {
                $logger->warning("Review ID {$row['id']} has an invalid or missing rating.");
            }

            $review->aggregate();
            echo "Review for product ID " . $row['product_id'] . " has been saved.\n";
         
        } catch (\Exception $e) {
            $logger->error('Error saving review: ' . $e->getMessage());
            echo 'Error saving review: ' . $e->getMessage() . "\n";
        }
    // }
    }
} catch (\Exception $e) {
    echo $e->getMessage();
}
