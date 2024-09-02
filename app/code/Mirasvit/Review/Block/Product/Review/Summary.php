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


namespace Mirasvit\Review\Block\Product\Review;


use Magento\Framework\View\Element\Template;
use Mirasvit\Review\Api\Data\ReviewSummaryInterface;
use Mirasvit\Review\Model\ConfigProvider;

class Summary extends Template
{
    private $configProvider;

    private $reviewSummary;

    public function __construct(
        ConfigProvider $configProvider,
        Template\Context $context,
        array $data = []
    ) {
        $this->configProvider = $configProvider;

        parent::__construct($context, $data);
    }

    public function setReviewSummary(?ReviewSummaryInterface $reviewSummary): self
    {
        $this->reviewSummary = $reviewSummary;

        return $this;
    }

    public function getReviewSummary(): ?ReviewSummaryInterface
    {
        return $this->reviewSummary;
    }

    public function isShowInfoHint(): bool
    {
        return $this->configProvider->isShowInfoHint();
    }

    protected function _toHtml()
    {
        if (!$this->configProvider->isAggregationEnabled()) {
            return '';
        }

        return parent::_toHtml();
    }
}
