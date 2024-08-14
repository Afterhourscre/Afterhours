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


namespace Mirasvit\Comment\Controller\Adminhtml;


use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Mirasvit\Comment\Repository\CommentRepository;

abstract class CommentAbstract extends Action
{
    protected $commentRepository;

    protected $context;

    public function __construct(
        CommentRepository $commentRepository,
        Context $context
    ) {
        $this->commentRepository = $commentRepository;
        $this->context           = $context;

        parent::__construct($context);
    }

    protected function _isAllowed(): bool
    {
        return $this->context->getAuthorization()->isAllowed('Mirasvit_Comment::comment_edit');
    }
}
