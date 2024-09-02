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


use Magento\Framework\View\Element\Template;
use Mirasvit\Comment\Api\Data\CommentInterface;
use Mirasvit\Comment\Repository\CommentRepository;

class Container extends Template
{
    protected $_template = 'Mirasvit_Comment::comment/container.phtml';

    protected $context;

    private   $commentRepository;

    private   $treeFactory;

    private   $commentFormFactory;

    public function __construct(
        CommentRepository $commentRepository,
        TreeFactory $treeFactory,
        FormFactory $commentFormFactory,
        Template\Context $context,
        array $data = []
    ) {
        $this->commentRepository  = $commentRepository;
        $this->treeFactory        = $treeFactory;
        $this->commentFormFactory = $commentFormFactory;
        $this->context            = $context;

        parent::__construct($context, $data);
    }

    public function getTree(): array
    {
        return $this->commentRepository->getTree(
            $this->getEntityType(),
            $this->getEntityId(),
            (int)$this->context->getStoreManager()->getStore()->getId()
        );
    }

    public function getCommentsCount(): int
    {
        return $this->commentRepository->getByEntity(
            $this->getEntityType(),
            $this->getEntityId()
        )->getSize();
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

    public function getCommentsHtml(array $items): string
    {
        return $this->treeFactory->create()
            ->setEntityType($this->getEntityType())
            ->setEntityId($this->getEntityId())
            ->setItems($items)
            ->toHtml();
    }

    public function getCommentFormHtml(): string
    {
        return $this->commentFormFactory->create()
            ->setEntityType($this->getEntityType())
            ->setEntityId($this->getEntityId())
            ->toHtml();
    }
}
