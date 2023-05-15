<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Helpdesk\Model\Mail;

use Magento\Framework\Mail\TemplateInterface;
use Magento\Email\Model\Template as MagentoEmailTemplate;
use Aheadworks\Helpdesk\Model\Mail\Sender as MailSender;
use Aheadworks\Helpdesk\Model\Mail\ThreadMessage\Filter as MailThreadMessageFilter;

/**
 * Class Template
 * @package Aheadworks\Helpdesk\Model\Mail
 */
class Template extends MagentoEmailTemplate implements TemplateInterface
{
    /**
     * {@inheritdoc}
     */
    public function getProcessedTemplate(array $variables = [])
    {
        $processedTemplate = parent::getProcessedTemplate($variables);
        if (isset($variables[MailSender::ADD_HDU_REPLIES_HISTORY_MARKER_FLAG_NAME])
            && $variables[MailSender::ADD_HDU_REPLIES_HISTORY_MARKER_FLAG_NAME]
        ) {
            $processedTemplate = $this->getRepliesHistoryMarker() . $processedTemplate;
        }
        return $processedTemplate;
    }

    /**
     * Get replies history marker
     *
     * @return string
     */
    private function getRepliesHistoryMarker()
    {
        $message = __('Please type your reply above this line.');
        $markerHtml = '<br><div class="aw-hdu-reply-marker">' . $message . '</div><br>';

        return MailThreadMessageFilter::REPLIES_HISTORY_MARKER . $markerHtml;
    }
}
