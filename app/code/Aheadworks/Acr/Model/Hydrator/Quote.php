<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Acr\Model\Hydrator;

use Magento\Framework\EntityManager\HydratorInterface;

/**
 * Class CartHistory
 * @package Aheadworks\Acr\Model
 */
class Quote implements HydratorInterface
{
    /**
     * {@inheritdoc}
     */
    public function extract($entity)
    {
        return $entity->getData();
    }

    /**
     * {@inheritdoc}
     */
    public function hydrate($entity, array $data)
    {
        $entity->setData(array_merge($entity->getData(), $data));
        return $entity;
    }
}
