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
use Mirasvit\SeoContent\Api\Data\TemplateInterface;
use Mirasvit\SeoContent\Api\Repository\TemplateRepositoryInterface;
use Mirasvit\SeoContent\Model\TemplateFactory;
use Mirasvit\SeoContent\Model\ResourceModel\Template\CollectionFactory;

class TemplateRepository implements TemplateRepositoryInterface
{
    private $factory;

    private $collectionFactory;

    private $entityManager;

    public function __construct(
        TemplateFactory $factory,
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
        $template = $this->create();
        $template = $this->entityManager->load($template, $id);

        if (!$template->getId()) {
            return false;
        }

        return $template;
    }

    public function save(TemplateInterface $template)
    {
        return $this->entityManager->save($template);
    }

    public function delete(TemplateInterface $template)
    {
        $this->entityManager->delete($template);

        return true;
    }
}
