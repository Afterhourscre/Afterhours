<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_CallForPrice
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\CallForPrice\Controller\Adminhtml\Grid;

use Mageplaza\CallForPrice\Controller\Adminhtml\Rules;

/**
 * Class ProductList
 * @package Mageplaza\CallForPrice\Controller\Adminhtml\Grid
 */
class ProductList extends Rules
{
    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        try {
            /** @var \Magento\Framework\View\Layout $layout */
            $layout   = $this->_view->getLayout();
            $block    = $layout->createBlock('Mageplaza\CallForPrice\Block\Adminhtml\Rules\Edit\Grid');
            $response = $block->toHtml();
        } catch (\Exception $exception) {
            $response = __('An error occurred');
            $this->_logger->critical($exception);
        }

        return $this->_response->setBody($response);
    }
}
