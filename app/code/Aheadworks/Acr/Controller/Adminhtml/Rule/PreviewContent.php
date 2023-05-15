<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Acr\Controller\Adminhtml\Rule;

/**
 * Class PreviewContent
 * @package Aheadworks\Acr\Controller\Adminhtml\Rule
 */
class PreviewContent extends \Aheadworks\Acr\Controller\Adminhtml\AbstractPreviewContent
{
    /**
     * {@inheritdoc}
     */
    public function getPreviewUrl($id = null)
    {
        return $this->urlBuilder->getUrl('aw_acr/rule/preview');
    }
}
