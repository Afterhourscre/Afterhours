<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Acr\Model\Sample;

use Magento\Framework\Module\Dir\Reader as DirReader;

/**
 * Class SchemaLocator
 * @package Aheadworks\Acr\Model\Sample
 */
class SchemaLocator implements \Magento\Framework\Config\SchemaLocatorInterface
{
    /**
     * @var string
     */
    private $schema;

    /**
     * @var string
     */
    private $perFileSchema;

    /**
     * SchemaLocator constructor.
     * @param DirReader $moduleReader
     */
    public function __construct(
        DirReader $moduleReader
    ) {
        $this->schema = $moduleReader->getModuleDir('etc', 'Aheadworks_Acr') . '/' . 'sample_data.xsd';
        $this->perFileSchema = $this->schema;
    }

    /**
     * {@inheritdoc}
     */
    public function getSchema()
    {
        return $this->schema;
    }

    /**
     * {@inheritdoc}
     */
    public function getPerFileSchema()
    {
        return $this->perFileSchema;
    }
}
