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


namespace Mirasvit\Comment\Block\Adminhtml\Comment;


use Magento\Backend\Block\Template;

class Form extends Template
{
    protected $_template = 'Mirasvit_Comment::comment/form.phtml';

    private $context;

    public function __construct(
        Template\Context $context,
        array $data = []
    ) {
        $this->context = $context;

        parent::__construct($context, $data);
    }

    public function getSubmitUrl(): string
    {
        return $this->getUrl('mst_comment/comment/post');
    }

    public function getFormId(): string
    {
        $formId = 'mst-comment-form-' . $this->getEntityType() . '-' . $this->getEntityId();

        if ($parentId = $this->getParentId()) {
            $formId .= '-parent-' . $parentId;
        }

        return $formId;
    }

    protected function _toHtml()
    {
        if (!$this->context->getAuthorization()->isAllowed('Mirasvit_Comment::comment_edit')) {
            return '';
        }

        return parent::_toHtml();
    }
}
