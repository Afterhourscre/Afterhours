<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Coupongenerator\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Store\Api\WebsiteRepositoryInterface;
use Magento\Framework\Convert\DataObject;

/**
 * Class Websites
 * @package Aheadworks\Coupongenerator\Model\Source
 */
class Websites implements OptionSourceInterface
{
    /**
     * @var WebsiteRepositoryInterface
     */
    private $websiteRepository;

    /**
     * @var DataObject
     */
    private $objectConverter;

    /**
     * @param WebsiteRepositoryInterface $websiteRepository
     * @param DataObject $objectConverter
     */
    public function __construct(
        WebsiteRepositoryInterface $websiteRepository,
        DataObject $objectConverter
    ) {
        $this->websiteRepository = $websiteRepository;
        $this->objectConverter = $objectConverter;
    }

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        $websites = [];
        foreach ($this->websiteRepository->getList() as $website) {
            if ($website->getId() != 0) {
                $websites[] = $website;
            }
        }
        return $this->objectConverter->toOptionArray($websites, 'id', 'name');
    }
}
