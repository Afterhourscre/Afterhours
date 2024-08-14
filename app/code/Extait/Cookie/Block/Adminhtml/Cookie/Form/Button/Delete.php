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

namespace Extait\Cookie\Block\Adminhtml\Cookie\Form\Button;

use Extait\Cookie\Api\Data\CookieInterface;
use Extait\Cookie\Block\Adminhtml\AbstractFormButton;

/** @api */
class Delete extends AbstractFormButton
{
    /**
     * @inheritDoc
     */
    public function getButtonData()
    {
        /** @var \Extait\Cookie\Api\Data\CookieInterface $cookie */
        $cookie = $this->registry->registry('current_cookie');
        $buttonData = [];

        if (isset($cookie) && empty($cookie->getIsSystem())) {
            $buttonData = [
                'label' => __('Delete'),
                'on_click' => 'deleteConfirm(\'' .
                    __('Are you sure want to do delete this cookie?') .
                    '\', \'' . $this->getUrl('*/*/delete', [CookieInterface::ID => $cookie->getId()]) . '\')',
                'class' => 'scalable delete',
                'sort_order' => 20,
            ];
        }

        return $buttonData;
    }
}
