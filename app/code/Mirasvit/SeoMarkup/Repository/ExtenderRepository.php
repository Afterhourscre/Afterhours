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
 * @package   mirasvit/module-seo
 * @version   2.9.6
 * @copyright Copyright (C) 2024 Mirasvit (https://mirasvit.com/)
 */


declare(strict_types=1);

namespace Mirasvit\SeoMarkup\Repository;

use Exception;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\Store;
use Mirasvit\Core\Service\SerializeService;
use Mirasvit\SeoMarkup\Api\Data\ExtenderInterface;
use Mirasvit\SeoMarkup\Api\Data\ExtenderInterfaceFactory as Factory;
use Mirasvit\SeoMarkup\Model\ResourceModel\Extender;
use Mirasvit\SeoMarkup\Model\ResourceModel\Extender\Collection;
use Mirasvit\SeoMarkup\Model\ResourceModel\Extender\CollectionFactory;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ExtenderRepository
{
    private $resource;

    private $factory;

    private $collectionFactory;

    public function __construct(
        Extender          $resource,
        Factory           $factory,
        CollectionFactory $collectionFactory
    ) {
        $this->resource          = $resource;
        $this->factory           = $factory;
        $this->collectionFactory = $collectionFactory;
    }

    public function getCollection(): Collection
    {
        return $this->collectionFactory->create();
    }

    /**
     * @return ExtenderInterface[]
     */
    public function getListForProduct(ProductInterface $product, string $entityTypeId, int $storeId): array
    {
        $snippetExtenders = [];
        $extenders        = $this->getCollection()
            ->addFieldToFilter(ExtenderInterface::IS_ACTIVE, 1)
            ->addFieldToFilter(ExtenderInterface::ENTITY_TYPE_ID, $entityTypeId)
            ->addFieldToFilter(
                ExtenderInterface::STORE_IDS,
                [['finset' => Store::DEFAULT_STORE_ID], ['finset' => $storeId]]
            )
            ->getItems();

        foreach ($extenders as $extender) {
            if ($extender->getConditions()->validate($product)) {
                $snippetArray = SerializeService::decode($extender->getSnippet());
                if ($snippetArray) {
                    $snippetExtenders[] = $extender->setSnippetArray($snippetArray);
                }
            }
        }

        return $snippetExtenders;
    }

    /**
     * @throws NoSuchEntityException
     */
    public function get(int $extenderId): ExtenderInterface
    {
        $extender = $this->factory->create();
        $this->resource->load($extender, $extenderId);
        if (!$extender->getId()) {
            throw new NoSuchEntityException(__('The Rich Snippet Extender with ID "%1" doesn\'t exist.', $extenderId));
        }

        return $extender;
    }

    /**
     * @throws CouldNotSaveException
     */
    public function save(ExtenderInterface $extender): ExtenderInterface
    {
        try {
            $this->resource->save($extender);
        } catch (Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }

        return $extender;
    }

    /**
     * @throws CouldNotDeleteException
     */
    public function delete(ExtenderInterface $extender): bool
    {
        try {
            $this->resource->delete($extender);
        } catch (Exception $exception) {
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }

        return true;
    }

    /**
     * @throws CouldNotDeleteException
     * @throws NoSuchEntityException
     */
    public function deleteById(int $extenderId): bool
    {
        return $this->delete($this->get($extenderId));
    }
}
