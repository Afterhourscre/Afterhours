<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the commercial license
 * that is bundled with this package in the file LICENSE.txt.
 *
 * @category Extait
 * @package Extait_Cookie
 * @copyright Copyright (c) 2016-2018 Extait, Inc. (http://www.extait.com)
 */

namespace Extait\Cookie\Block\Adminhtml\Category\Form\Button;

use Extait\Cookie\Api\Data\CategoryInterface;
use Extait\Cookie\Block\Adminhtml\AbstractFormButton;

/** @api */
class Delete extends AbstractFormButton
{
    /**
     * @inheritDoc
     */
    public function getButtonData()
    {
        /** @var \Extait\Cookie\Api\Data\CategoryInterface $category */
        $category = $this->registry->registry('current_category');
        $buttonData = [];

        if (isset($category) && empty($category->getIsSystem())) {
            $buttonData = [
                'label' => __('Delete'),
                'on_click' => 'deleteConfirm(\'' .
                    __('Are you sure want to do delete this category?') .
                    '\', \'' . $this->getUrl('*/*/delete', [CategoryInterface::ID => $category->getId()]) . '\')',
                'class' => 'scalable delete',
                'sort_order' => 20,
            ];
        }

        return $buttonData;
    }
}
