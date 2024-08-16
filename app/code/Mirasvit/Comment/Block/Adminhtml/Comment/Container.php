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
use Magento\Framework\Module\Manager;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Registry;
use Mirasvit\Comment\Api\Data\CommentInterface;
use Mirasvit\Comment\Model\ConfigProvider;
use Mirasvit\Comment\Repository\CommentRepository;

class Container extends Template
{
    protected $_template = "Mirasvit_Comment::comment/container.phtml";

    private $coreRegistry;

    private $moduleManager;

    private $objectManager;

    private $commentRepository;

    private $configProvider;

    private $commentFormFactory;

    private $treeFactory;

    private $context;

    public function __construct(
        Registry $coreRegistry,
        Manager $moduleManager,
        ObjectManagerInterface $objectManager,
        ConfigProvider $configProvider,
        FormFactory $commentFormFactory,
        TreeFactory $treeFactory,
        CommentRepository $commentRepository,
        Template\Context $context,
        array $data = []
    ) {
        $this->coreRegistry       = $coreRegistry;
        $this->moduleManager      = $moduleManager;
        $this->objectManager      = $objectManager;
        $this->commentRepository  = $commentRepository;
        $this->configProvider     = $configProvider;
        $this->commentFormFactory = $commentFormFactory;
        $this->treeFactory        = $treeFactory;
        $this->context            = $context;

        parent::__construct($context, $data);
    }

    public function getCommmentFormHtml(int $commentId = null): string
    {
        $form = $this->commentFormFactory->create()
            ->setEntityType($this->getEntityType())
            ->setEntityId($this->getEntityId());

        if ($commentId) {
            $form->setParentId($commentId);
        }

        return $form->toHtml();
    }

    public function getCommentsHtml(array $items): string
    {
        return $this->treeFactory->create()
            ->setEntityType($this->getEntityType())
            ->setEntityId($this->getEntityId())
            ->setItems($items)
            ->toHtml();
    }

    public function getEntityType(): ?string
    {
        if ($this->getReview()) {
            return CommentInterface::REL_ENTITY_TYPE_REVIEW;
        }

        if ($this->getPost()) {
            return CommentInterface::REL_ENTITY_TYPE_BLOG_POST;
        }

        return null;
    }

    public function getEntityId(): ?int
    {
        if ($review = $this->getReview()) {
            return (int)$review->getId();
        }

        if ($blogPost = $this->getPost()) {
            return (int)$blogPost->getId();
        }

        return null;
    }

    private function getPost()
    {
        if (
            $this->moduleManager->isEnabled('Mirasvit_BlogMx')
            && class_exists('\Mirasvit\BlogMx\Registry')
        ) {
            /** @var \Mirasvit\BlogMx\Registry $blogRegistry */
            $blogRegistry = $this->objectManager->get('\Mirasvit\BlogMx\Registry');

            return $blogRegistry->getPost();
        }

        return null;
    }

    private function getReview()
    {
        return $this->coreRegistry->registry('review_data');
    }

    public function getTree(): array
    {
        return $this->commentRepository->getTree(
            $this->getEntityType(),
            $this->getEntityId(),
            0,
            false
        );
    }

    protected function _toHtml()
    {
        if (!$this->getEntityType() || !$this->getEntityId()) {
            return '';
        }

        return parent::_toHtml();
    }

    public function isAllowedToSeeComments(): bool
    {
        return $this->context->getAuthorization()->isAllowed('Mirasvit_Comment::comment_all');
    }
}
