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
 * @version   2.0.169
 * @copyright Copyright (C) 2020 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\SeoContent\Repository;

use Magento\Framework\EntityManager\EntityManager;
use Mirasvit\SeoContent\Api\Data\RewriteInterface;
use Mirasvit\SeoContent\Api\Repository\RewriteRepositoryInterface;
use Mirasvit\SeoContent\Model\RewriteFactory;
use Mirasvit\SeoContent\Model\ResourceModel\Rewrite\CollectionFactory;

class RewriteRepository implements RewriteRepositoryInterface
{
    private $factory;

    private $collectionFactory;

    private $entityManager;

    public function __construct(
        RewriteFactory $factory,
        CollectionFactory $collectionFactory,
        EntityManager $entityManager
    ) {
        $this->factory = $factory;
        $this->collectionFactory = $collectionFactory;
        $this->entityManager = $entityManager;
    }

    public function getCollection()
    {
        return $this->collectionFactory->create();
    }

    /**
     * {@inheritdoc}
     */
    public function create()
    {
        return $this->factory->create();
    }

    public function get($id)
    {
        $rewrite = $this->create();
        $rewrite = $this->entityManager->load($rewrite, $id);

        if (!$rewrite->getId()) {
            return false;
        }

        return $rewrite;
    }

    public function save(RewriteInterface $rewrite)
    {
        return $this->entityManager->save($rewrite);
    }

    public function delete(RewriteInterface $rewrite)
    {
        $this->entityManager->delete($rewrite);

        return true;
    }
}
