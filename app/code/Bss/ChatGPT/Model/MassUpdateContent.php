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
declare(strict_types=1);

namespace Bss\ChatGPT\Model;

use Bss\ChatGPT\Model\APIChatGPT;
use Magento\Catalog\Model\Indexer\Product\Flat\Processor;
use Magento\Catalog\Model\Product\Action;
use Magento\Catalog\Model\ProductFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\EntityManager\EntityManager;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\TemporaryStateExceptionInterface;
use Magento\Framework\Bulk\OperationInterface;
use Magento\Framework\Serialize\SerializerInterface;
use Psr\Log\LoggerInterface;

/**
 * Consumer for export message.
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class MassUpdateContent
{
    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @var \Magento\Catalog\Model\Indexer\Product\Flat\Processor
     */
    private $productFlatIndexerProcessor;

    /**
     * @var \Magento\Catalog\Model\Product\Action
     */
    private $productAction;

    /**
     * @var \Magento\Framework\Serialize\SerializerInterface
     */
    private $serializer;

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var ChatGPT
     */
    private $modelChatGPT;

    /**
     * @var ProductFactory
     */
    private $productFactory;

    /**
     * @var APIChatGPT
     */
    private $apiChatGPT;

    /**
     * @param APIChatGPT $apiChatGPT
     * @param ProductFactory $productFactory
     * @param ChatGPT $modelChatGPT
     * @param Processor $productFlatIndexerProcessor
     * @param Action $action
     * @param LoggerInterface $logger
     * @param SerializerInterface $serializer
     * @param EntityManager $entityManager
     */
    public function __construct(
        APIChatGPT $apiChatGPT,
        ProductFactory $productFactory,
        ChatGPT $modelChatGPT,
        \Magento\Catalog\Model\Indexer\Product\Flat\Processor $productFlatIndexerProcessor,
        \Magento\Catalog\Model\Product\Action $action,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Serialize\SerializerInterface $serializer,
        EntityManager $entityManager
    ) {
        $this->apiChatGPT = $apiChatGPT;
        $this->productFactory = $productFactory;
        $this->modelChatGPT = $modelChatGPT;
        $this->productFlatIndexerProcessor = $productFlatIndexerProcessor;
        $this->productAction = $action;
        $this->logger = $logger;
        $this->serializer = $serializer;
        $this->entityManager = $entityManager;
    }

    /**
     * Process
     *
     * @param \Magento\AsynchronousOperations\Api\Data\OperationInterface $operation
     * @throws \Exception
     *
     * @return void
     */
    public function process(\Magento\AsynchronousOperations\Api\Data\OperationInterface $operation)
    {
        try {
            $serializedData = $operation->getSerializedData();
            $data = $this->serializer->unserialize($serializedData);
            $this->execute($data);
        } catch (\Zend_Db_Adapter_Exception $e) {
            $this->logger->critical($e->getMessage());
            if ($e instanceof \Magento\Framework\DB\Adapter\LockWaitException
                || $e instanceof \Magento\Framework\DB\Adapter\DeadlockException
                || $e instanceof \Magento\Framework\DB\Adapter\ConnectionException
            ) {
                $status = OperationInterface::STATUS_TYPE_RETRIABLY_FAILED;
                $errorCode = $e->getCode();
                $message = $e->getMessage();
            } else {
                $status = OperationInterface::STATUS_TYPE_NOT_RETRIABLY_FAILED;
                $errorCode = $e->getCode();
                $message = __(
                    'Sorry, something went wrong during product content update. Please see log for details.'
                );
            }
        } catch (NoSuchEntityException $e) {
            $this->logger->critical($e->getMessage());
            $status = ($e instanceof TemporaryStateExceptionInterface)
                ? OperationInterface::STATUS_TYPE_RETRIABLY_FAILED
                : OperationInterface::STATUS_TYPE_NOT_RETRIABLY_FAILED;
            $errorCode = $e->getCode();
            $message = $e->getMessage();
        } catch (LocalizedException $e) {
            $this->logger->critical($e->getMessage());
            $status = OperationInterface::STATUS_TYPE_NOT_RETRIABLY_FAILED;
            $errorCode = $e->getCode();
            $message = $e->getMessage();
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());
            $status = OperationInterface::STATUS_TYPE_NOT_RETRIABLY_FAILED;
            $errorCode = $e->getCode();
            $message = $e->getMessage() . __('Please see log for details.');
        }

        $operation->setStatus($status ?? OperationInterface::STATUS_TYPE_COMPLETE)
            ->setErrorCode($errorCode ?? null)
            ->setResultMessage($message ?? null);

        $this->entityManager->save($operation);
    }

    /**
     * Execute
     *
     * @param array $data
     *
     * @return void
     * @throws LocalizedException
     * @throws \Exception
     */
    public function execute($data): void
    {
        try {
            $dataApi = $data['data_api'];
            $start = strpos($dataApi['prompt'], "{{")+2;
            $length = strpos($dataApi['prompt'], "}}") - $start;
            $stringAttribute = substr($dataApi['prompt'], $start, $length);
            $content = [
                "success" => "",
                "error" => ""
            ];

            foreach ($data['product_ids'] as $productId) {
                $dataApi['prompt'] = $data['data_api']['prompt'];
                $product = $this->productFactory->create()->load($productId);
                if (isset($dataApi['attributes']) && isset($dataApi['prompt'])) {
                    //Check lại từ api gửi lên attr để xử lý lại chuỗi
                    $promptAttribute = $this->modelChatGPT->getAttributesProduct($product, $dataApi['attributes']);

                    $dataApi['prompt'] = str_replace($stringAttribute, $promptAttribute, $dataApi['prompt']);
                    if (!$promptAttribute) {
                        unset($dataApi['attributes']);
                        $dataApi['prompt'] = str_replace("{{}}", "", $dataApi['prompt']);
                    } else {
                        $this->modelChatGPT->prepareDataApiAttributes($product, $dataApi);
                    }
                }

                $content = $this->apiChatGPT->callChatGPT($dataApi, $content);
                if ($content['success']) {
                    if (isset($data['type']) && $product->getData($data['type'])) {
                        $oldContent = $product->getData($data['type']);
                        $specialString = " ";
                        if (in_array($data['type'], ["meta_description", "meta_keyword"])) {
                            $specialString = ", ";
                        }
                        if (strpos($oldContent, '<div data-content-type="html" data-appearance="default" data-element="main">') !== false) {
                            $oldContent = str_replace("</div>", " ", $oldContent);
                            $attributeUpdate[$data['type']] = $oldContent . $specialString . $content['success'] . "</div>";
                        } else {
                            $attributeUpdate[$data['type']] = $oldContent . $specialString . $content['success'];
                        }
                    } else {
                        $attributeUpdate[$data['type']] = $content['success'];
                    }

                    $this->productAction->updateAttributes([$productId], $attributeUpdate, $data['store_id']);
                    array_splice($data['product_ids'], array_search($productId,$data['product_ids']), 1);
                } else {
                    $this->logger->critical(json_encode($content['error']));
                    throw new \Exception(
                        "Products with id: " . implode(", ",$data['product_ids']) . " updated fail!\n " . $content['error'] . " \n"
                    );
                }
            }
            $this->productFlatIndexerProcessor->reindexList($data['product_ids']);
        } catch (\Exception $e) {
            throw new \Exception(
                $e->getMessage()
            );
        }
    }
}
