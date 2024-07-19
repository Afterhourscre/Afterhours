<?php
namespace WeltPixel\OwlCarouselSlider\Block\Product;

use Magento\Catalog\Helper\ImageFactory as HelperFactory;
use Magento\Catalog\Model\Product;
class ImageBuilder extends \Magento\Catalog\Block\Product\ImageBuilder
{
    /**
     * @var \WeltPixel\OwlCarouselSlider\Helper\Custom
     */
    protected $_helperCustom;

    /**
     * @param HelperFactory $helperFactory
     * @param \Magento\Catalog\Block\Product\ImageFactory $imageFactory
     * @param \WeltPixel\OwlCarouselSlider\Helper\Custom $_helperCustom
     */
    public function __construct(
        HelperFactory $helperFactory,
        \Magento\Catalog\Block\Product\ImageFactory $imageFactory,
        \WeltPixel\OwlCarouselSlider\Helper\Custom $_helperCustom
    ) {
        $this->_helperCustom = $_helperCustom;
        parent::__construct($helperFactory, $imageFactory);
    }



    /**
     * Create image block
     *
     * @return \Magento\Catalog\Block\Product\Image
     */
  public function create(Product $product = null, string $imageId = null, array $attributes = null)
{
    /** Check if module is enabled */
    if (!$this->_helperCustom->isHoverImageEnabled() && !$this->isLazyLoadEnabled()) {
        return parent::create($product, $imageId, $attributes); // Pass arguments to parent method
    }

    // Initialize helpers and data
    $helper = $this->helperFactory->create()->init($product, $imageId);
    $template = $helper->getFrame()
        ? 'WeltPixel_OwlCarouselSlider::product/image.phtml'
        : 'WeltPixel_OwlCarouselSlider::product/image_with_borders.phtml';

    $data = [
        'data' => [
            'template' => $template,
            'image_url' => $helper->getUrl(),
            'width' => $helper->getWidth(),
            'height' => $helper->getHeight(),
            'label' => $helper->getLabel(),
            'ratio' => $this->getRatio($helper),
            'custom_attributes' => $this->getCustomAttributes(),
            'resized_image_width' => $helper->getResizedImageInfo()[0] ?? $helper->getWidth(),
            'resized_image_height' => $helper->getResizedImageInfo()[1] ?? $helper->getHeight(),
        ],
    ];

    // Handle hover images if enabled
    if ($this->_helperCustom->isHoverImageEnabled() && in_array($imageId, [
        'related_products_list',
        'upsell_products_list',
        'cart_cross_sell_products',
        'new_products_content_widget_grid'
    ])) {
        $hoverHelper = $this->helperFactory->create()->init($product, $imageId . '_hover')
            ->resize($helper->getWidth(), $helper->getHeight());

        $hoverImageUrl = $hoverHelper->getUrl();
        $placeHolderUrl = $hoverHelper->getDefaultPlaceholderUrl();

        // Determine hover image URL
        $data['data']['hover_image_url'] = ($placeHolderUrl == $hoverImageUrl) ? null : $hoverImageUrl;
    }

    // Handle lazy loading if enabled
    if ($this->isLazyLoadEnabled()) {
        $data['data']['lazy_load'] = true;
    }

    // Create and return the image block
    return $this->imageFactory->create($data);
}


    /**
     * @return bool
     */
    protected function isLazyLoadEnabled() {
        foreach ($this->attributes as $name => $value) {
            if ($name == 'weltpixel_lazyLoad') {
                return true;
            }
        }

        return false;
    }

    /**
     * Retrieve image custom attributes for HTML element
     *
     * @return string
     */
    protected function getCustomAttributes()
    {
        $result = [];
        foreach ($this->attributes as $name => $value) {
            if ($name == 'weltpixel_lazyLoad') continue;
            $result[] = $name . '="' . $value . '"';
        }
        return !empty($result) ? implode(' ', $result) : '';
    }

}