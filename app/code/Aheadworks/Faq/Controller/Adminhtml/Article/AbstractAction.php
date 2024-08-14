<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */


namespace Aheadworks\Faq\Controller\Adminhtml\Article;

use Magento\Backend\App\Action;

/**
 * Class AbstractAction
 */
abstract class AbstractAction extends Action
{
    /**
     * Authorization level of a basic admin session
     */
    const ADMIN_RESOURCE = 'Aheadworks_Faq::article';
}
