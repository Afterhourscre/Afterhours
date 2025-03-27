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


use Magento\Framework\DataObject;
use Magento\Framework\View\Element\Template;
use Mirasvit\Review\Model\ConfigProvider;

class ProsCons extends Template
{
    private $review;

    private $configProvider;

    protected $_template = "Mirasvit_Review::review/proscons.phtml";

    public function __construct(ConfigProvider $configProvider, Template\Context $context, array $data = [])
    {
        $this->configProvider = $configProvider;

        parent::__construct($context, $data);
    }

    public function setReview(DataObject $review): self
    {
        $this->review = $review;

        return $this;
    }

    public function fieldToArray(string $field): array
    {
        if (!$this->review || !$this->review->getData($field)) {
            return [];
        }

        return explode(PHP_EOL, $this->review->getData($field));
    }

    protected function _toHtml(): string
    {
        if (!$this->configProvider->displayProsAndCons()) {
            return '';
        }

        return parent::_toHtml();
    }
}
