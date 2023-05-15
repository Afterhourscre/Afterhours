<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Coupongenerator\Ui\DataProvider;

use Magento\Framework\View\Element\UiComponent\DataProvider\DataProviderInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Api\Filter;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Magento\SalesRule\Api\RuleRepositoryInterface;
use Aheadworks\Coupongenerator\Model\Converter\Salesrule as SalesruleConverter;
use Aheadworks\Coupongenerator\Model\SalesruleRepository;

/**
 * Class RuleDataProvider
 * @package Aheadworks\Coupongenerator\Ui\DataProvider
 * @codeCoverageIgnore
 */
class RuleDataProvider extends AbstractDataProvider implements DataProviderInterface
{
    /**
     * @var RuleRepositoryInterface
     */
    private $ruleRepository;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var SalesruleConverter
     */
    private $salesruleConverter;

    /**
     * @var SalesruleRepository
     */
    private $salesruleRepository;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param RuleRepositoryInterface $ruleRepository
     * @param RequestInterface $request
     * @param SalesruleConverter $salesruleConverter
     * @param SalesruleRepository $salesruleRepository
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        RuleRepositoryInterface $ruleRepository,
        RequestInterface $request,
        SalesruleConverter $salesruleConverter,
        SalesruleRepository $salesruleRepository,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->ruleRepository = $ruleRepository;
        $this->request = $request;
        $this->salesruleConverter = $salesruleConverter;
        $this->salesruleRepository = $salesruleRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        $data = [];
        $id = $this->request->getParam($this->getRequestFieldName());

        if ($id) {
            /** @var \Aheadworks\Coupongenerator\Api\Data\SalesruleInterface $salesruleDataObject */
            $salesruleDataObject = $this->salesruleRepository->get($id);

            /** @var \Magento\SalesRule\Api\Data\RuleInterface $ruleDataObject */
            $ruleDataObject = $this->ruleRepository->getById($salesruleDataObject->getRuleId());

            $formData = $this->salesruleConverter->toFormData($ruleDataObject);

            $data[$salesruleDataObject->getId()] = $formData;
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function addFilter(Filter $filter)
    {
        return $this;
    }
}
