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


namespace Mirasvit\Comment\Plugin\Ui\Review;


use Mirasvit\Comment\Api\Data\CommentInterface;
use Mirasvit\Comment\Repository\CommentRepository;

class AddCommentsCountDataToListingPlugin
{
    private $commentRepository;

    public function __construct(CommentRepository $commentRepository)
    {
        $this->commentRepository = $commentRepository;
    }

    public function afterGetData($subject, array $result): array
    {
        foreach ($result['items'] as $idx => $item) {
            $commentsCount = $this->commentRepository->getByEntity(
                CommentInterface::REL_ENTITY_TYPE_REVIEW,
                (int)$item['review_id'],
                false
            )->getSize();

            $result['items'][$idx]['comments_count'] = $commentsCount;
        }

        return $result;
    }
}
