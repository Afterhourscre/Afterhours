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


namespace Mirasvit\Comment\Observer;


use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Mirasvit\Comment\Api\Data\CommentInterface;
use Mirasvit\Comment\Repository\CommentRepository;

class BlogPostDeleteObserver implements ObserverInterface
{
    private $commentRepository;

    public function __construct(CommentRepository $commentRepository)
    {
        $this->commentRepository = $commentRepository;
    }

    public function execute(Observer $observer)
    {
        $object = $observer->getData('object');

        if (!$object || !$object->getId()) {
            return;
        }

        $this->commentRepository->deleteByEntity(
            CommentInterface::REL_ENTITY_TYPE_BLOG_POST,
            (int)$object->getId()
        );
    }
}
