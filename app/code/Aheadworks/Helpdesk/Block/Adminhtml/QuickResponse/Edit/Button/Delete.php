<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Helpdesk\Block\Adminhtml\QuickResponse\Edit\Button;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Magento\Backend\Block\Widget\Context;

/**
 * Class Delete
 * @package Aheadworks\Helpdesk\Block\Adminhtml\QuickResponse\Edit\Button
 */
class Delete implements ButtonProviderInterface
{
    /**
     * @var Context
     */
    private $context;

    /**
     * @param Context $context
     */
    public function __construct(
        Context $context
    ) {
        $this->context = $context;
    }

    /**
     * @return array
     */
    public function getButtonData()
    {
        $data = [];
        $quickResponseId = $this->context->getRequest()->getParam('id');
        if ($quickResponseId) {
            $data = [
                'label' => __('Delete'),
                'class' => 'delete',
                'on_click' => 'deleteConfirm(\'' . __(
                    'Are you sure you want to do this?'
                ) . '\', \'' . $this->getDeleteUrl($quickResponseId) . '\')',
                'sort_order' => 20,
            ];
        }
        return $data;
    }

    /**
     * Generate url by quick response ID
     *
     * @param $quickResponseId
     * @return mixed
     */
    public function getDeleteUrl($quickResponseId)
    {
        return $this->context->getUrlBuilder()->getUrl('*/*/delete', ['id' => $quickResponseId]);
    }
}
