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


namespace Mirasvit\Comment\Block\Comment;


use Magento\Customer\Model\Session;
use Magento\Framework\Data\Form\FormKey;
use Magento\Framework\View\Element\Template;
use Mirasvit\Comment\Api\Data\CommentInterface;
use Mirasvit\Comment\Model\ConfigProvider;

class Form extends Template
{
    protected $_template = 'Mirasvit_Comment::comment/form.phtml';

    protected $context;

    private $customerSession;

    private $configProvider;

    private $formKey;

    /** @var bool|null */
    private $commentsAllowed = null;

    public function __construct(
        ConfigProvider $configProvider,
        FormKey $formKey,
        Session $customerSession,
        Template\Context $context,
        array $data = []
    )  {
        $this->configProvider    = $configProvider;
        $this->customerSession   = $customerSession;
        $this->formKey           = $formKey;
        $this->context           = $context;

        parent::__construct($context, $data);
    }

    public function isCommentAllowed(): bool
    {
        if (is_null($this->commentsAllowed)) {
            $this->commentsAllowed = ($this->customerSession->isSessionExists() && $this->customerSession->getCustomerGroupId())
                || $this->configProvider->isGuestCommentAllowed();
        }

        return $this->commentsAllowed;
    }

    public function getFormKey(): string
    {
        return $this->formKey->getFormKey();
    }

    public function getSubmitUrl(): string
    {
        return $this->getUrl('mst_comment/comment/post');
    }

    public function getEntityType(): string
    {
        return $this->getData(CommentInterface::REL_ENTITY_TYPE);
    }

    public function getEntityId(): int
    {
        return (int)$this->getData(CommentInterface::REL_ENTITY_ID);
    }

    public function setEntityType(string $type): self
    {
        return $this->setData(CommentInterface::REL_ENTITY_TYPE, $type);
    }

    public function setEntityId(int $id): self
    {
        return $this->setData(CommentInterface::REL_ENTITY_ID, $id);
    }

    public function getParentId(): ?int
    {
        return $this->getData(CommentInterface::PARENT_ID)
            ? (int)$this->getData(CommentInterface::PARENT_ID)
            : null;
    }

    public function setParentId(int $parentId): self
    {
        return $this->setData(CommentInterface::PARENT_ID, $parentId);
    }

    public function getFormId(): string
    {
        $formId = 'mst-comment-form-' . $this->getEntityType() . '-' . $this->getEntityId();

        if ($parentId = $this->getParentId()) {
            $formId .= '-parent-' . $parentId;
        }

        return $formId;
    }
}
