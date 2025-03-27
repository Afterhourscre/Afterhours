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
use Magento\Framework\View\Element\Template;
use Mirasvit\Comment\Api\Data\CommentInterface;
use Mirasvit\Comment\Model\ConfigProvider;

class Tree extends Template
{
    protected $_template = 'Mirasvit_Comment::comment/tree.phtml';

    private $commentFormFactory;

    private $configProvider;

    private $customerSession;

    /** @var bool|null */
    private $commentsAllowed = null;

    public function __construct(
        ConfigProvider $configProvider,
        FormFactory $commentFormFactory,
        Session $customerSession,
        Template\Context $context,
        array $data = []
    ) {
        $this->configProvider     = $configProvider;
        $this->commentFormFactory = $commentFormFactory;
        $this->customerSession    = $customerSession;

        parent::__construct($context, $data);
    }

    /** @var CommentInterface[]|null */
    private $items = null;

    public function setItems(?array $items): self
    {
        $this->items = $items;

        return $this;
    }

    /** @return CommentInterface[]|null */
    public function getItems(): ?array
    {
        return $this->items;
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

    public function getCommmentFormHtml(int $commentId): string
    {
        return $this->commentFormFactory->create()
            ->setEntityType($this->getEntityType())
            ->setEntityId($this->getEntityId())
            ->setParentId($commentId)
            ->toHtml();
    }

    public function getFormattedDate(string $date): string
    {
        $dateFormat = $this->configProvider->getDateFormatValue();
        $isShowTime = $this->configProvider->getDateFormatIsShowTime();

        return $this->formatDate($date, $dateFormat, $isShowTime);
    }

    public function isCommentAllowed(): bool
    {
        if (is_null($this->commentsAllowed)) {
            $this->commentsAllowed = ($this->customerSession->isSessionExists() && $this->customerSession->getCustomerGroupId())
                || $this->configProvider->isGuestCommentAllowed();
        }

        return $this->commentsAllowed;
    }
}
