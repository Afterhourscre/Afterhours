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


namespace Mirasvit\Review\Plugin\Backend\AdminNotification;


use Magento\Framework\View\Element\UiComponent\DataProvider\DataProviderInterface;
use Mirasvit\Review\Model\System\Message\PendingReviewMessage;

class PendingReviewNotificationTypePlugin
{
    public function afterGetData(DataProviderInterface $subject, array $result): array
    {
        foreach ($result['items'] as $idx => $message) {
            if ($message['identity'] == PendingReviewMessage::IDENTITY) {
                $message['status'] = 0;
                $result['items'][$idx] = $message;
            }
        }

        return $result;
    }
}
