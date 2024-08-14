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

namespace Mirasvit\Review\Ui\Review\Form\Control;

use Mirasvit\Review\Api\Data\ReviewInterface;

class BackButton extends ButtonAbstract
{
    public function getButtonData(): array
    {
        $url        = $this->getUrl('*/*/');
        $productId  = $this->context->getRequest()->getParam('product_id');
        $customerId = $this->context->getRequest()->getParam('customer_id');

        if ($productId) {
            $url = $this->getUrl('catalog/product/edit', ['id' => $productId]);
        } elseif ($customerId) {
            $url = $this->getUrl('customer/index/edit', ['id' => $customerId]);
        }

        return [
            'label'      => __('Back'),
            'on_click'   => sprintf("location.href = '%s';", $url),
            'class'      => 'back',
            'sort_order' => 10,
        ];
    }
}
