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
 * @package   mirasvit/module-review
 * @version   1.1.2
 * @copyright Copyright (C) 2024 Mirasvit (https://mirasvit.com/)
 */


declare(strict_types=1);


namespace Mirasvit\Review\Ui\Review\Form\Control;


use Magento\Backend\Block\Widget\Context;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Mirasvit\Review\Api\Data\ReviewInterface;
use Mirasvit\Review\Repository\ReviewRepository;


abstract class ButtonAbstract implements ButtonProviderInterface
{
    protected $context;

    protected $repository;

    public function __construct(
        Context $context,
        ReviewRepository $repository
    ) {
        $this->context    = $context;
        $this->repository = $repository;
    }

    public function getId(): ?int
    {
        $id = $this->context->getRequest()->getParam(ReviewInterface::ID);

        return $id ? (int)$id : null;
    }

    public function getUrl(string $route = '', array $params = []): string
    {
        return $this->context->getUrlBuilder()->getUrl($route, $params);
    }

    protected function getNextReview(): ?ReviewInterface
    {
        $id = $this->getId();

        if (!$id) {
            return null;
        }

        $nextReview = $this->repository->getCollection(false)
            ->addFieldToFilter('main_table.'.ReviewInterface::ID, ['gt' => $id])
            ->getFirstItem();

        if (!$nextReview || !$nextReview->getId()) {
            return null;
        }

        return $nextReview;
    }

    protected function getPrevReview(): ?ReviewInterface
    {
        $id = $this->getId();

        if (!$id) {
            return null;
        }

        $prevReview = $this->repository->getCollection(false)
            ->addFieldToFilter('main_table.'.ReviewInterface::ID, ['lt' => $id])
            ->getLastItem();

        if (!$prevReview || !$prevReview->getId()) {
            return null;
        }

        return $prevReview;
    }
}
