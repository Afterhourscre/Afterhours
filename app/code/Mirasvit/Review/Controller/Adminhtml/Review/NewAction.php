<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-review
 * @version   1.1.2
 * @copyright Copyright (C) 2024 Mirasvit (https://mirasvit.com/)
 */


declare(strict_types=1);


namespace Mirasvit\Review\Controller\Adminhtml\Review;


use Magento\Framework\Controller\ResultFactory;
use Mirasvit\Review\Controller\Adminhtml\ReviewAbstract;

class NewAction extends ReviewAbstract
{
    public function execute()
    {
//        return $this->resultRedirectFactory->create()
//            ->setPath('*/*/edit');

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Mirasvit_Review::review_all');
        $resultPage->getConfig()->getTitle()->prepend(__('Customer Reviews'));
        $resultPage->getConfig()->getTitle()->prepend(__('New Review'));
        $resultPage->addContent($resultPage->getLayout()->createBlock(\Magento\Review\Block\Adminhtml\Add::class));
        $resultPage->addContent($resultPage->getLayout()->createBlock(
            \Magento\Review\Block\Adminhtml\Product\Grid::class
        ));
        return $resultPage;
    }
}
