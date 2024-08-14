<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Acr\Model;

use Aheadworks\Acr\Model\Sample\Reader\Xml as XmlReader;

/**
 * Class Sample
 * @package Aheadworks\Acr\Model
 */
class Sample
{
    /**
     * @var  XmlReader
     */
    private $xmlReader;

    /**
     * @param XmlReader $reader
     */
    public function __construct(
        XmlReader $reader
    ) {
        $this->xmlReader = $reader;
    }

    /**
     * Get sample data
     *
     * @return array
     */
    public function get()
    {
        $data = $this->xmlReader->read();
        return $data;
    }
}
