<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Helpdesk\Model\Mail\ThreadMessage;

/**
 * Class Filter
 * @package Aheadworks\Helpdesk\Model\Mail\ThreadMessage
 */
class Filter
{
    /**
     * RegEx pattern to detect previous replies
     */
    const REPLIES_HISTORY_REGEX = '/(<!--){1}(\sHDU_REPLY_MARKER)[\s\S]*/';
    const REPLIES_HISTORY_MARKER = '<!-- HDU_REPLY_MARKER -->';

    /**
     * Cut history of previous replies
     *
     * @param string $content
     * @return string
     */
    public function cutRepliesHistory($content)
    {
        return preg_replace(self::REPLIES_HISTORY_REGEX, '', $content);
    }
}
