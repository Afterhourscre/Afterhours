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


namespace Mirasvit\Review\Block\Widget\Review;


use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\View\Element\Template;
use Mirasvit\Review\Api\Data\ReviewInterface;
use Mirasvit\Review\Block\Product\Review;
use Mirasvit\Review\Model\ConfigProvider;
use Mirasvit\Review\Repository\ReviewMediaRepository;

class Card extends Review
{
    protected $_template = 'Mirasvit_Review::widget/review/card.phtml';

    private $reviewMediaRepository;

    public function __construct(
        ReviewMediaRepository $reviewMediaRepository,
        ConfigProvider $configProvider,
        Template\Context $context,
        array $data = []
    ) {
        $this->reviewMediaRepository = $reviewMediaRepository;

        parent::__construct($configProvider, $context, $data);
    }

    public function getMainImageUrl(): ?string
    {
        $list = $this->reviewMediaRepository->getByReview((int)$this->getReview()->getId());

        if (!$list) {
            return null;
        }

        $image = array_first($list);

        return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA)
            . 'mst_review/'
            . $image->getReviewId() . '/'
            . $image->getValue();
    }
}
