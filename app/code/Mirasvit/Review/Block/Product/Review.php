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


namespace Mirasvit\Review\Block\Product;


use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\View\Element\Template;
use Mirasvit\Review\Api\Data\ReviewInterface;
use Mirasvit\Review\Model\ConfigProvider;

class Review extends Template
{
    private $configProvider;

    private $review;

    private $isShowGallery = true;

    protected $_template = "Mirasvit_Review::review.phtml";

    public function __construct(
        ConfigProvider $configProvider,
        Template\Context $context,
        array $data = []
    ) {
        $this->configProvider = $configProvider;

        parent::__construct($context, $data);
    }

    public function setReview(ReviewInterface $review): self
    {
        $this->review = $review;

        $this->review->loadRatingVotes();

        return $this;
    }

    public function getReview(): ?ReviewInterface
    {
        return $this->review;
    }

    public function setIsShowGallery(bool $isShow): self
    {
        $this->isShowGallery = $isShow;

        return $this;
    }

    public function isShowGallery(): bool
    {
        return $this->configProvider->isMediaEnabled() && $this->isShowGallery;
    }

    public function displayProsAndCons(): bool
    {
        return $this->configProvider->displayProsAndCons();
    }

    public function displayVerifiedBuyer(): bool
    {
        return $this->configProvider->displayVerifiedBuyer();
    }

    public function displayLocation(): bool
    {
        return $this->configProvider->displayCountry();
    }

    public function getLocation(ReviewInterface $review): ?string
    {
        if (!$this->displayLocation()) {
            return null;
        }

        $country  = $review->getCountry()
            ? $review->getCountry()
            : null;

        if (!$country) {
            return null;
        }

        $location = $review->getLocation();

        return $location ? $location . ', ' . $country : $country;
    }

    public function getCountryCode(ReviewInterface $review): ?string
    {
        if (!$this->displayLocation()) {
            return $result;
        }

        $code = $review->getCountryIso() ? strtolower($review->getCountryIso()) : 'us';


        return $code;
    }

    public function displayProductOption(): bool
    {
        return $this->displayVerifiedBuyer() && $this->configProvider->displayProductOption();
    }

    public function getProductOptions(ReviewInterface $review): ?string
    {
        if (!$review->getIsVerifiedBuyer()) {
            return null;
        }

        if (!is_array($review->getProductInfo())) {
            return null;
        }

        $result = '';

        foreach ($review->getProductInfo() as $attribute) {
            $result .= $attribute['label'] . ': ' . $attribute['value'] . ' ';
        }

        return $result;
    }

    public function getFormattedDate(string $date): string
    {
        $dateFormat = $this->configProvider->getDateFormatValue();
        $isShowTime = $this->configProvider->getDateFormatIsShowTime();

        return $this->formatDate($date, $dateFormat, $isShowTime);
    }
}
