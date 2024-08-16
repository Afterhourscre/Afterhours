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
 * @package   mirasvit/module-comment
 * @version   1.0.1
 * @copyright Copyright (C) 2024 Mirasvit (https://mirasvit.com/)
 */


declare(strict_types=1);


namespace Mirasvit\Comment\Plugin\Backend\AdminNotification;


use Magento\Framework\View\Element\UiComponent\DataProvider\DataProviderInterface;
use Mirasvit\Comment\Model\System\Message\PendingCommentMessage;

class PendingCommentNotificationTypePlugin
{
    public function afterGetData(DataProviderInterface $subject, array $result): array
    {
//        var_dump($result);

        foreach ($result['items'] as $idx => $message) {
            if ($message['identity'] == PendingCommentMessage::IDENTITY) {
                $message['status'] = 0;
                $message['actions'] = [
                    'details' => [
                        'callback' => [
                            [
                                'provider' => 'notification_area.notification_area.commentModalContainer.commentModal',
                                'target' => 'openModal',
                            ]
                        ],
                        'href' => '#',
                        'label' => __('View Details'),
                    ]
                ];

                $result['items'][$idx] = $message;
            }
        }

        return $result;
    }

    public function afterGetMeta($subject, $result)
    {
//        if (isset($result['items'])) {
//            var_dump($result);
//        }

        return $result;
    }
}
