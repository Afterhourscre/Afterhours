<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\RewardPoints\Model\EarnRule\Action;

/**
 * Class TypePool
 * @package Aheadworks\RewardPoints\Model\EarnRule\Action
 */
class TypePool
{
    /**
     * @var TypeInterface[]
     */
    private $types;

    /**
     * @param array $types
     */
    public function __construct(
        $types = []
    ) {
        $this->types = $types;
    }

    /**
     * Get types
     *
     * @return TypeInterface[]
     * @throws \Exception
     */
    public function getTypes()
    {
        return $this->types;
    }

    /**
     * Get types count
     *
     * @return int
     * @throws \Exception
     */
    public function getTypesCount()
    {
        $types = $this->getTypes();
        return count($types);
    }

    /**
     * Get type by code
     *
     * @param string $code
     * @return TypeInterface
     * @throws \Exception
     */
    public function getTypeByCode($code)
    {
        $types = $this->getTypes();
        if (!isset($types[$code])) {
            throw new \Exception(sprintf('Unknown action type: %s requested', $code));
        }

        return $types[$code];
    }

    /**
     * Check if the type exists
     *
     * @param string $code
     * @return bool
     */
    public function isTypeExists($code)
    {
        $result = false;
        try {
            $types = $this->getTypes();
            $result = isset($types[$code]);
        } catch (\Exception $e) {
        }

        return $result;
    }
}
