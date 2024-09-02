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


namespace Mirasvit\Review\Controller\Adminhtml\Review;


use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\ForwardFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Filesystem;
use Magento\Framework\Registry;
use Magento\Review\Model\RatingFactory;
use Mirasvit\Core\Service\SerializeService;
use Mirasvit\Review\Api\Data\ReviewInterface;
use Mirasvit\Review\Api\Data\ReviewMediaInterface;
use Mirasvit\Review\Controller\Adminhtml\ReviewAbstract;
use Mirasvit\Review\Repository\ReviewRepository;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Save extends ReviewAbstract
{
    protected $mediaDirectory;

    protected $filesystem;

    public function __construct(
        Filesystem       $filesystem,
        RatingFactory    $ratingFactory,
        ReviewRepository $repository,
        ForwardFactory   $resultForwardFactory,
        Registry         $registry,
        Context          $context
    ) {
        $this->filesystem     = $filesystem;
        $this->mediaDirectory = $filesystem->getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
        parent::__construct($ratingFactory, $repository, $resultForwardFactory, $registry, $context);
    }

    /**
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        $model = $this->initModel();

        if (($reviewId = $this->getRequest()->getParam(ReviewInterface::ID)) && !$model->getId()) {
            $this->messageManager->addErrorMessage((string)__('The review was removed by another user or does not exist.'));

            return $resultRedirect->setPath('*/*/');
        }

        $data = $this->getRequest()->getPostValue();

        if (isset($data['photos']) && $data['photos']) {
            if (is_array($data['photos'])) {
                $photos = $data['photos'];
            } else {
                $photos = SerializeService::decode($data['photos']);
            }

            foreach ($photos as $key => $photo) {
                if (isset($photo['file'])) {
                    $extension                                        = ($photo && isset($photo['name'])) ? pathinfo($photo['name'], PATHINFO_EXTENSION) : null;
                    $photos[$key]['hashedName']                       = hash('sha256', $photo['name'] . strval(time())) . '.' . $extension;
                    $data['media'][$key][ReviewMediaInterface::VALUE] = $photos[$key]['hashedName'];
                } else {
                    $data['media'][$key][ReviewMediaInterface::VALUE] = $photos[$key]['name'];
                }

                $data['media'][$key][ReviewMediaInterface::PRODUCT_ID] = $data['product_id'];
                $data['media'][$key][ReviewMediaInterface::TYPE]       = 'image';
            }

        }

        $data['updateMedia'] = true;

        $redirectReviewId = $this->getRequest()->getParam('proceed');

        try {
            if (isset($data['select_stores'])) {
                $data['stores'] = $data['select_stores'];
            }

            $model->setData($data);
            $model->setProductId((int)$data['product_id'])
                ->setEntityId($model->getEntityIdByCode('product'));

            if (isset($data['select_stores'])) {
                $model->setStoreId(\Magento\Store\Model\Store::DEFAULT_STORE_ID);
            }

            $this->reviewRepository->save($model);

            if (isset($photos)) {
                $result = $this->moveUploadFiles($photos, (int)$model->getId());
            }

            $arrRatingId = $this->getRequest()->getParam('ratings', []);
            /** @var \Magento\Review\Model\Rating\Option\Vote $votes */
            $votes = $this->_objectManager->create(\Magento\Review\Model\Rating\Option\Vote::class)
                ->getResourceCollection()
                ->setReviewFilter($reviewId)
                ->addOptionInfo()
                ->load()
                ->addRatingOptions();
            foreach ($arrRatingId as $ratingId => $optionId) {
                if ($vote = $votes->getItemByColumnValue('rating_id', $ratingId)) {
                    $this->ratingFactory->create()
                        ->setVoteId($vote->getId())
                        ->setReviewId($model->getId())
                        ->updateOptionVote($optionId);
                } else {
                    $this->ratingFactory->create()
                        ->setRatingId($ratingId)
                        ->setReviewId($model->getId())
                        ->addOptionVote($optionId, $model->getProductId());
                }
            }

            $model->aggregate();

            $this->messageManager->addSuccessMessage(__('You saved the review.'));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        if ($redirectReviewId) { // Save and Next, Save and Previous
            return $resultRedirect->setPath('*/*/edit', [ReviewInterface::ID => $redirectReviewId]);
        }

        return $resultRedirect->setPath('*/*/');
    }

    protected function _isAllowed(): bool
    {
        if (parent::_isAllowed()) {
            return true;
        }

        if ($this->getModel()->getStatusId() != ReviewInterface::STATUS_PENDING) {
            $this->messageManager->addErrorMessage(
                __(
                    'You don’t have permission to perform this operation.'
                    . ' The selected review must be in Pending Status.'
                )
            );

            return false;
        }

        return true;
    }

    public function moveUploadFiles(array $photos, int $id)
    {
        $mediaFolder = 'mst_review/' . $id . '/';

        try {

            if ($photos) {
                foreach ($photos as $photo) {
                    if (!isset($photo['file'])) {
                        continue;
                    }

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
