<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Acr\Controller\Adminhtml\Queue;

/**
 * Class PreviewContent
 * @package Aheadworks\Acr\Controller\Adminhtml\Queue
 */
class PreviewContent extends \Aheadworks\Acr\Controller\Adminhtml\AbstractPreviewContent
{
    /**
     * {@inheritdoc}
     */
    public function getPreviewUrl($id = null)
    {
        return $this->urlBuilder->getUrl('aw_acr/queue/preview', ['id' => $id]);
    }
}
