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


namespace Mirasvit\Review\Ui\Review\Form;

use Magento\Backend\Helper\Data as BackendHelper;
use Magento\Customer\Model\Customer;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Review\Block\Adminhtml\Rating\Summary;
use Magento\Review\Block\Adminhtml\Rating\Detailed;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Mirasvit\Core\Service\SerializeService;
use Mirasvit\Review\Api\Data\ReviewInterface;
use Mirasvit\Review\Api\Data\ReviewMediaInterface;
use Mirasvit\Review\Model\ConfigProvider;
use Mirasvit\Review\Model\Review;
use Mirasvit\Review\Repository\ReviewRepository;
use Mirasvit\Review\Repository\ReviewMediaRepository;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Ui\DataProvider\Modifier\PoolInterface;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class DataProvider extends AbstractDataProvider
{
    private $backendHelper;

    private $configProvider;

    private $repository;

    private $mediaRepository;

    private $context;

    private $ratingSummaryBlock;

    private $ratingDetailedBlock;

    private $storeManager;

    private $url;

    private $scopeConfig;

    /**
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        ScopeConfigInterface            $scopeConfig,
        ReviewMediaRepository           $mediaRepository,
        ConfigProvider                  $configProvider,
        BackendHelper                   $backendHelper,
        ReviewRepository                $repository,
        Summary                         $ratingSummaryBlock,
        StoreManagerInterface           $storeManager,
        Detailed                        $ratingDetailedBlock,
        ContextInterface                $context,
        \Magento\Framework\UrlInterface $url,
        string                          $name,
        string                          $primaryFieldName,
        string                          $requestFieldName,
        array                           $meta = [],
        array                           $data = []
    ) {
        $this->scopeConfig         = $scopeConfig;
        $this->url                 = $url;
        $this->storeManager        = $storeManager;
        $this->mediaRepository     = $mediaRepository;
        $this->configProvider      = $configProvider;
        $this->backendHelper       = $backendHelper;
        $this->repository          = $repository;
        $this->collection          = $this->repository->getCollection(false);
        $this->ratingSummaryBlock  = $ratingSummaryBlock;
        $this->ratingDetailedBlock = $ratingDetailedBlock;
        $this->context             = $context;

        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    public function getMeta()
    {

        $meta = parent::getMeta();

        $meta['general'] = [
            'children' => [
                'photo[]' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'label'           => __('Photos'),
                                'sortOrder'       => 100,
                                'collapsible'     => true,
                                'componentType'   => 'field',
                                'formElement'     => 'imageUploader',
                                'component'       => 'Mirasvit_Review/js/image-uploader',
                                'previewTmpl'     => 'Mirasvit_Review/form/preview',
                                'template'        => 'Mirasvit_Review/form/element/uploader/image',
                                'isMultipleFiles' => 1,
                                'dataScope'       => 'photo[]',
                                'resizeConfig'    => (object)$this->getResizeConfig(),
                                'uploaderConfig'  => [
                                    'maxNumFiles' => $this->configProvider->getMaxNumberFiles(),
                                    'url' => $this->url->getUrl('*/*/upload'),
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];


        return $meta;
    }

    public function getData(): array
    {
        $result = [];

        /** @var Review $model */
        if ($model = $this->getModel()) {

            $reviewData = $model->getData();

            if ($product = $model->getProduct()) {
                $productUrl            = $this->backendHelper->getUrl('catalog/product/edit', ['id' => $product->getId()]);
                $reviewData['product'] = '<a href="' . $productUrl . '">' . $product->getName() . '</a>';
            }

            $author = '';

            /** @var Customer $customer */
            if ($customer = $model->getCustomer()) {
                $customerUrl = $this->backendHelper->getUrl('customer/index/edit', ['id' => $customer->getId()]);
                $author      = '<a href="' . $customerUrl . '">' . $customer->getName()
                    . '</a> <a href="mailto:' . $customer->getEmail() . '">(' . $customer->getEmail() . ')</a>';
            } else {
                $author = __('Guest');

                if ($model->getCustomerId()) {
                    $author = __("Customer (removed)");
                }

                if ($model->getStoreId() == \Magento\Store\Model\Store::DEFAULT_STORE_ID) {
                    $author = __("Administrator");
                }
            }
            $media = $this->getMediaFiles((int)$reviewData[ReviewInterface::ID]);

            $reviewData['photos']            = $media ? SerializeService::encode($media) : null;
            $reviewData['photo[]']           = $media;
            $reviewData['display_pros_cons'] = $this->configProvider->displayProsAndCons();
            $reviewData['author']            = $author;
            $reviewData['product_id']        = $model->getProductId();
            $reviewData['rating_summary']    = $this->ratingSummaryBlock->toHtml();
            $reviewData['rating_detailed']   = $this->ratingDetailedBlock->setTemplate('Mirasvit_Review::rating/detailed.phtml')->toHtml();
            $reviewData['store_disabled']    = true;

            $result[$model->getId()] = $reviewData;
        }

        return $result;
    }

    private function getModel(): ?ReviewInterface
    {
        $id = $this->context->getRequestParam(ReviewInterface::ID, null);

        return $id ? $this->repository->get((int)$id) : null;
    }

    private function getMediaFiles(int $reviewId): ?array
    {
        $media  = [];
        $images = $this->mediaRepository->getByReview($reviewId);


        if ($images) {
            foreach ($images as $image) {
                $media[] = [
                    'name' => $image[ReviewMediaInterface::VALUE],
                    'url'  => $this->getMediaUrl($reviewId, $image[ReviewMediaInterface::VALUE]),
                ];
            }

            return $media;
        }

        return null;

    }

    public function getMediaUrl(int $reviewId, string $image = '')
    {
        $mediaUrl = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'mst_review/' . $reviewId . '/' . $image;

        return $mediaUrl;
    }

    public function getResizeConfig(): array
    {
        $config = ['action' => 'resizeImage'];

        if (!$this->scopeConfig->getValue('system/upload_configuration/enable_resize')) {
            return $config;
        }

        $maxWidth  = $this->scopeConfig->getValue('system/upload_configuration/max_width') ? : 1920;
        $maxHeight = $this->scopeConfig->getValue('system/upload_configuration/enable_resize') ? : 1200;

        $config['maxWidth']  = $maxWidth;
        $config['maxHeight'] = $maxWidth;

        return $config;
    }
}
