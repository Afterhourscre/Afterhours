<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageCloud\MageWorxOptionTemplates\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\RedirectFactory;
use MageCloud\MageWorxOptionTemplates\Controller\Adminhtml\Group\BuilderBulk as Builder;
//use MageWorx\OptionTemplates\Controller\Adminhtml\Group\Builder as Builder;

abstract class Group extends \Magento\Backend\App\Action
{
    /**
     * @var RedirectFactory
     */
    protected $resultRedirectFactory;

    /**
     * @var Builder
     */
    protected $groupBuilder;

    /**
     *
     * @param Builder $groupBuilder
     * @param Context $context
     */
    public function __construct(
        Builder $groupBuilder,
        Context $context
    ) {
        $this->groupBuilder = $groupBuilder;
        $this->resultRedirectFactory = $context->getResultRedirectFactory();
        parent::__construct($context);
    }

    /**
     * @return array|mixed
     */
    public function getProducts()
    {
        if (!$this->getId()) {
            return [];
        }
        $array = $this->getData('products');
        if ($array === null) {
            $array = $this->getResource()->getProducts($this);
            $this->setData('products', $array);
        }

        return $array;
    }

    /**
     * Is access to section allowed
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('MageWorx_OptionTemplates::groups');
    }
}
