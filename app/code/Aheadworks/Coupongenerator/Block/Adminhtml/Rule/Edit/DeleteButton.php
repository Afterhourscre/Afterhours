<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Coupongenerator\Block\Adminhtml\Rule\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Magento\Backend\Block\Widget\Context;
use Aheadworks\Coupongenerator\Model\SalesruleRepository;

/**
 * Class DeleteButton
 * @package Aheadworks\Coupongenerator\Block\Adminhtml\Rule\Edit
 */
class DeleteButton implements ButtonProviderInterface
{
    /**
     * @var Context
     */
    private $context;

    /**
     * @var SalesruleRepository
     */
    private $salesruleRepository;

    /**
     * @param Context $context
     * @param SalesruleRepository $salesruleRepository
     */
    public function __construct(
        Context $context,
        SalesruleRepository $salesruleRepository
    ) {
        $this->context = $context;
        $this->salesruleRepository = $salesruleRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function getButtonData()
    {
        $data = [];
        if ($id = $this->getId()) {
            $data = [
                'label' => __('Delete'),
                'class' => 'delete',
                'on_click' => sprintf(
                    "deleteConfirm('%s', '%s')",
                    __('Are you sure you want to do this?'),
                    $this->getUrl('*/*/delete', ['id' => $id])
                ),
                'sort_order' => 20,
            ];
        }
        return $data;
    }

    /**
     * Return rule ID
     *
     * @return int|null
     */
    public function getId()
    {
        $id = $this->context->getRequest()->getParam('id');

        if ($id && $this->salesruleRepository->get($id)) {
            return $id;
        }

        return null;
    }

    /**
     * Generate url by route and parameters
     *
     * @param string $route
     * @param array $params
     * @return string
     */
    public function getUrl($route = '', $params = [])
    {
        return $this->context->getUrlBuilder()->getUrl($route, $params);
    }
}
