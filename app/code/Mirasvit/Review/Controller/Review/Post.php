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


namespace Mirasvit\Review\Controller\Review;

use Magento\Framework\App\Action\HttpPostActionInterface as HttpPostActionInterface;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\MessageQueue\PublisherInterface;
use Magento\Framework\Registry as CoreRegistry;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Review\Controller\Product as ProductController;
use Magento\Review\Model\Review;
use Magento\Review\Model\ReviewFactory as MagentoReviewFactory;
use Magento\Review\Model\RatingFactory;
use Magento\Catalog\Model\Design as CatalogDesign;
use Magento\Framework\Session\Generic as ReviewSession;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Data\Form\FormKey\Validator as FormKeyValidator;
use Mirasvit\Core\Service\SerializeService;
use Mirasvit\Review\Api\Data\ReviewInterface;
use Mirasvit\Review\Api\Data\ReviewMediaInterface;
use Mirasvit\Review\Model\ConfigProvider;
use Mirasvit\Review\Repository\ReviewRepository;
use Magento\Framework\Filesystem;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Mirasvit\Review\Service\Email\NotificationService;
use Psr\Log\LoggerInterface;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Post extends ProductController implements HttpPostActionInterface
{
    protected $filesystem;

    protected $fileUploader;

    protected $mediaDirectory;

    private   $reviewRepository;

    private $publisher;

    private $configProvider;

    /**
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Filesystem                  $filesystem,
        UploaderFactory             $fileUploader,
        ReviewRepository            $reviewRepository,
        PublisherInterface          $publisher,
        ConfigProvider              $configProvider,
        Context                     $context,
        CoreRegistry                $coreRegistry,
        CustomerSession             $customerSession,
        CategoryRepositoryInterface $categoryRepository,
        LoggerInterface             $logger,
        ProductRepositoryInterface  $productRepository,
        MagentoReviewFactory        $reviewFactory,
        RatingFactory               $ratingFactory,
        CatalogDesign               $catalogDesign,
        ReviewSession               $reviewSession,
        StoreManagerInterface       $storeManager,
        FormKeyValidator            $formKeyValidator
    ) {
        $this->filesystem       = $filesystem;
        $this->fileUploader     = $fileUploader;
        $this->reviewRepository = $reviewRepository;
        $this->mediaDirectory   = $filesystem->getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
        $this->publisher        = $publisher;
        $this->configProvider   = $configProvider;

        parent::__construct(
            $context,
            $coreRegistry,
            $customerSession,
            $categoryRepository,
            $logger,
            $productRepository,
            $reviewFactory,
            $ratingFactory,
            $catalogDesign,
            $reviewSession,
            $storeManager,
            $formKeyValidator
        );
    }

    /**
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        if (!$this->formKeyValidator->validate($this->getRequest())) {
            $resultRedirect->setUrl($this->_redirect->getRefererUrl());

            return $resultRedirect;
        }

        $data = $this->reviewSession->getFormData(true);
        if ($data) {
            $rating = [];
            if (isset($data['ratings']) && is_array($data['ratings'])) {
                $rating = $data['ratings'];
            }
        } else {
            $data   = $this->getRequest()->getPostValue();
            $rating = $this->getRequest()->getParam('ratings', []);
        }

        if (($product = $this->initProduct()) && !empty($data)) {
            /** @var ReviewInterface $review */

            if (isset($data['photos']) && $data['photos']) {
                $photos = SerializeService::decode($data['photos']);

                foreach ($photos as $key => $photo) {
                    $extension                                             = ($photo && isset($photo['name'])) ? pathinfo($photo['name'], PATHINFO_EXTENSION) : null;
                    $photos[$key]['hashedName']                            = hash('sha256', $photo['name'] . strval(time())) . '.' . $extension;
                    $data['media'][$key][ReviewMediaInterface::VALUE]      = $photos[$key]['hashedName'];
                    $data['media'][$key][ReviewMediaInterface::PRODUCT_ID] = (int)$product->getId();
                    $data['media'][$key][ReviewMediaInterface::TYPE]       = 'image';
                }

            }

            $data['updateMedia'] = true;

            $review = $this->reviewRepository->create()->setData($data);
            $review->unsetData('review_id');

            $validate = $review->validate();
            if ($validate === true) {
                try {
                    $review->setEntityId($review->getEntityIdByCode('product'))
                        ->setProductId((int)$product->getId())
                        ->setStatusId(ReviewInterface::STATUS_PENDING)
                        ->setCustomerId((int)$this->customerSession->getCustomerId())
                        ->setStoreId((int)$this->storeManager->getStore()->getId())
                        ->setStores([$this->storeManager->getStore()->getId()]);

                    $this->reviewRepository->save($review);

                    if (isset($photos)) {
                        $result = $this->moveUploadFiles($photos, (int)$review->getId());
                    }

                    foreach ($rating as $ratingId => $optionId) {
                        $this->ratingFactory->create()
                            ->setRatingId($ratingId)
                            ->setReviewId($review->getId())
                            ->setCustomerId($this->customerSession->getCustomerId())
                            ->addOptionVote($optionId, $product->getId());
                    }

                    $review->aggregate();
                    $this->messageManager->addSuccessMessage(__('You submitted your review for moderation.'));

                    if ($this->configProvider->isEmailNotificationsForPendingReviewsEnabled()) {
                        $this->publisher->publish('mirasvit.review.notify_new', (int)$review->getId());
                    }
                } catch (\Exception $e) {
                    $this->reviewSession->setFormData($data);
                    $this->messageManager->addErrorMessage(__('We can\'t post your review right now.'));
                }
            } else {
                $this->reviewSession->setFormData($data);
                if (is_array($validate)) {
                    foreach ($validate as $errorMessage) {
                        $this->messageManager->addErrorMessage($errorMessage);
                    }
                } else {
                    $this->messageManager->addErrorMessage(__('We can\'t post your review right now.'));
                }
            }
        }

        $redirectUrl = $this->reviewSession->getRedirectUrl(true);
        $resultRedirect->setUrl($redirectUrl ? : $this->_redirect->getRedirectUrl());

        return $resultRedirect;
    }

    public function moveUploadFiles(array $photos, int $id)
    {
        $mediaFolder = 'mst_review/' . $id . '/';

        try {
            if ($photos) {
                foreach ($photos as $key => $photo) {
                    $target = $this->mediaDirectory->getAbsolutePath($mediaFolder);
                    if (!is_dir($target)) {
                        mkdir($target);
                    }
                    rename($photo['path'] . $photo['file'], $target . $photo['hashedName']);
                }

                return true;
            }

        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        }

        return false;
    }
}
