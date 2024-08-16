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
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\ResourceModel\CustomerRepository;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Mirasvit\Comment\Model\Config\Source\StatusSource;
use Mirasvit\Comment\Repository\CommentRepository;

class Tree extends Template
{
    protected $_template = "Mirasvit_Comment::comment/tree.phtml";

    private $coreRegistry;

    private $commentRepository;

    private $customerRepository;

    private $commentFormFactory;

    private $statusSource;

    private $context;

    public function __construct(
        Registry $coreRegistry,
        CommentRepository $commentRepository,
        FormFactory $commentFormFactory,
        CustomerRepository $customerRepository,
        StatusSource $statusSource,
        Template\Context $context,
        array $data = []
    ) {
        $this->coreRegistry       = $coreRegistry;
        $this->commentRepository  = $commentRepository;
        $this->commentFormFactory = $commentFormFactory;
        $this->customerRepository = $customerRepository;
        $this->statusSource       = $statusSource;
        $this->context            = $context;

        parent::__construct($context, $data);
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

    public function getCustomer(int $customerId = null): ?CustomerInterface
    {
        if (!$customerId) {
            return null;
        }

        try {
            return $this->customerRepository->getById($customerId);
        } catch (NoSuchEntityException $e) {
            return null;
        }

    }

    public function getStatusOptions(): array
    {
        return $this->statusSource->toOptionArray();
    }

    public function getStatusLabel(string $code): string
    {
        foreach ($this->getStatusOptions() as $option) {
            if ($option['value'] == $code) {
                return $option['label'];
            }
        }

        return '';
    }

    public function isAllowedToEditComments(): bool
    {
        return $this->context->getAuthorization()->isAllowed('Mirasvit_Comment::comment_edit');
    }
}
