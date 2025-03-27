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
 * @copyright  Copyright (c) 2024 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */

namespace Bss\ChatGPT\Controller\Adminhtml\ChatGPT;

use Bss\ChatGPT\Model\APIChatGPT;
use Bss\ChatGPT\Model\ChatGPT;
use Bss\ChatGPT\Model\Config;
use Magento\AsynchronousOperations\Api\Data\OperationInterface;
use Magento\Authorization\Model\UserContextInterface;
use Magento\Backend\App\Action\Context;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\Bulk\BulkManagementInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\DataObject\IdentityGeneratorInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Setup\Console\InputValidationException;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Catalog\Model\ProductFactory;
use Magento\AsynchronousOperations\Api\Data\OperationInterfaceFactory;
use Psr\Log\LoggerInterface;

class MassAction extends \Bss\ChatGPT\Controller\Adminhtml\ChatGPT\Api implements \Magento\Framework\App\Action\HttpPostActionInterface
{
    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var BulkManagementInterface
     */
    protected $bulkManagement;

    /**
     * @var IdentityGeneratorInterface
     */
    protected $identityService;

    /**
     * @var UserContextInterface
     */
    protected $userContext;

    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * @var OperationInterfaceFactory
     */
    protected $operationFactory;

    /**
     * @var ProductFactory
     */
    protected $productFactory;

    /**
     * @var ChatGPT
     */
    protected $modelChatGPT;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var \Magento\Catalog\Model\ProductRepository
     */
    protected $productRepository;

    /**
     * @param OperationInterfaceFactory $operartionFactory
     * @param ProductFactory $productFactory
     * @param ChatGPT $modelChatGPT
     * @param SerializerInterface $serializer
     * @param UserContextInterface $userContext
     * @param IdentityGeneratorInterface $identityService
     * @param BulkManagementInterface $bulkManagement
     * @param CollectionFactory $collectionFactory
     * @param Filter $filter
     * @param Context $context
     * @param Curl $curl
     * @param JsonFactory $resultJsonFactory
     * @param Config $config
     * @param Json $json
     * @param LoggerInterface $logger
     */
    public function __construct(
        OperationInterfaceFactory                                $operartionFactory,
        APIChatGPT                                               $apiChatGPT,
        ProductFactory                                           $productFactory,
        ChatGPT                                                  $modelChatGPT,
        \Magento\Framework\Serialize\SerializerInterface         $serializer,
        \Magento\Authorization\Model\UserContextInterface        $userContext,
        \Magento\Framework\DataObject\IdentityGeneratorInterface $identityService,
        \Magento\Framework\Bulk\BulkManagementInterface          $bulkManagement,
        CollectionFactory                                        $collectionFactory,
        Filter                                                   $filter,
        Context                                                  $context,
        \Magento\Framework\HTTP\Client\Curl                      $curl,
        \Magento\Framework\Controller\Result\JsonFactory         $resultJsonFactory,
        \Bss\ChatGPT\Model\Config                                $config,
        \Magento\Framework\Serialize\Serializer\Json             $json,
        \Psr\Log\LoggerInterface                                 $logger,
        \Magento\Catalog\Model\ProductRepository                  $productRepository
    ) {
        $this->modelChatGPT = $modelChatGPT;
        $this->productFactory = $productFactory;
        $this->operationFactory = $operartionFactory;
        $this->serializer = $serializer;
        $this->userContext = $userContext;
        $this->identityService = $identityService;
        $this->bulkManagement = $bulkManagement;
        $this->collectionFactory = $collectionFactory;
        $this->filter = $filter;
        $this->logger = $logger;
        $this->productRepository = $productRepository;
        parent::__construct($apiChatGPT, $context, $resultJsonFactory, $config, $json);
    }

    /**
     * @throws LocalizedException
     */
    public function execute()
    {
        $request = $this->getRequest()->getParams() ?? [];

        if (!$this->checkRequestParam($request)) {
            throw new InputValidationException(__('Validation Error.'));
        }

        if (!$this->config->isEnable()) {
            throw new LocalizedException(__('Please enable ChatGPT module.'));
        }

        $storeId = $this->apiChatGPT->getDefaultStoreId();
        $productIds = $this->getProductIds();
        if (empty($productIds)) {
            return $this->resultRedirectFactory->create()->setPath('catalog/product/', ['store' => $storeId]);
        }


        try {
            $this->publish($productIds, $request, $storeId);
            $this->messageManager->addSuccessMessage(__('Message of content product is added to queue'));
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage(
                $e,
                __('Something went wrong while updating the product(s) content.')
            );
        }

        return $this->resultRedirectFactory->create()->setPath('catalog/product/', ['store' => $storeId]);
    }

    /**
     * @param $request
     * @return bool
     */
    protected function checkRequestParam(&$request)
    {
        if (
            is_array($request)
            && array_key_exists('id', $request)
            && array_key_exists('search', $request)
            && array_key_exists('system_role', $request)
        ) {
            if (!array_key_exists('attributes', $request)
                || !is_array($request['attributes'])
            ) {
                $request['all_attribute'] = true;
            }
            return true;
        }
        return false;
    }

    /**
     * @return array|mixed
     */
    protected function getProductIds()
    {
        $error = false;
        try {
            $collection = $this->filter->getCollection($this->collectionFactory->create());
            $productIds = $collection->getAllIds();
        } catch (\Exception $e) {
            $productIds = $this->getRequest()->getParam('product_ids');
        }
        if (!is_array($productIds)) {
            $error = __('Please select products for attributes update.');
        } elseif (!$this->productFactory->create()->isProductsHasSku($productIds)) {
            $error = __('Please make sure to define SKU values for all processed products.');
        }

        if ($error) {
            $this->messageManager->addErrorMessage($error);
            $productIds = [];
        }

        return $productIds;
    }

    /**
     * @param $request
     * @return mixed
     */
    public function getDataApi($request)
    {
        $dataApi['system_role'] = $request['system_role'] ?? $this->config->getDefaultSystemRole();
        $dataApi['prompt'] = $request['search'] ?? $this->config->getDefaultPrompt();
        if (isset($request['all_attribute']) && $request['all_attribute']) {
            $dataApi['attributes'] = $this->modelChatGPT->prepareAttributesApiWithUseAllAttributes();
        } else {
            $dataApi['attributes'] = $request['attributes'];
        }
        $this->getConfigApi($dataApi);
        return $dataApi;
    }

    /**
     * @param $productIds
     * @param $request
     * @param $storeId
     * @return void
     * @throws LocalizedException
     */
    protected function publish($productIds, $request, $storeId)
    {
        $bulkUuid = $this->identityService->generateId();
        $productIdsChunks = array_chunk($productIds, 100);
        $bulkDescription = __('Update content for ' . count($productIds) . ' selected products');
        $operations = [];

        if ($productIdsChunks) {
            foreach ($productIdsChunks as $productIdsChunk) {
                $operations[] = $this->makeOperation(
                    'Update content product',
                    'product_content_update.chatgpt',
                    $this->getDataApi($request),
                    $request['id'],
                    $storeId,
                    $this->apiChatGPT->getWebsiteId($storeId),
                    $productIdsChunk,
                    $bulkUuid
                );
            }

            if (!empty($operations)) {
                $result = $this->bulkManagement->scheduleBulk(
                    $bulkUuid,
                    $operations,
                    $bulkDescription,
                    $this->userContext->getUserId()
                );
                if (!$result) {
                    throw new LocalizedException(
                        __('Something went wrong while processing the request create content.')
                    );
                }
            }
        }
    }

    /**
     * @param $meta
     * @param $queue
     * @param $dataApi
     * @param $type
     * @param $storeId
     * @param $websiteId
     * @param $productIds
     * @param $bulkUuid
     * @return OperationInterface
     */
    private function makeOperation(
        $meta,
        $queue,
        $dataApi,
        $type,
        $storeId,
        $websiteId,
        $productIds,
        $bulkUuid
    )
    {
        $dataToEncode = [
            'meta_information' => $meta,
            'product_ids' => $productIds,
            'store_id' => $storeId,
            'website_id' => $websiteId,
            'data_api' => $dataApi,
            'type' => $type
        ];
        $data = [
            'data' => [
                'bulk_uuid' => $bulkUuid,
                'topic_name' => $queue,
                'serialized_data' => $this->serializer->serialize($dataToEncode),
                'status' => \Magento\Framework\Bulk\OperationInterface::STATUS_TYPE_OPEN,
            ]
        ];

        return $this->operationFactory->create($data);
    }
}
